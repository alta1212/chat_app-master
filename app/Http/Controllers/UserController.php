<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use App\Models\friends;
use Directory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Pusher\Pusher;
use \DomainException;
use \InvalidArgumentException;
use \UnexpectedValueException;
use \DateTime;
use Carbon\Carbon;
class UserController extends Controller
{
    public function login()
    {
       return view("auth.login");
    }
    public function createUser(Request $request)
    {
    
         //kiểm tra đầu vào
         //resources/lang/en/validation.php nơi quy tắc được đặt
        $request->validate([
         'name'=>'required|max:20',
         'email'=>'required|email|unique:user',
         'password'=>'required|min:5|max:100',

        ]);

         //dùng câu query
         $query=DB::table('user')->insert(
            [
               'email' =>$request->email,#mail
               'pass' =>Hash::make($request->password),//hash để mã hoá mật khẩu 
               'name' =>$request->name,
               'token'=>$this->create($request->email),
               'avata'=>'DefaultAvt.jpg',
               'mota'=>'This person has not set a description',
               'joindate'=>Carbon::now()->toDateString(),
               'diachi'=>'This person has not set a adress'
            ]
         );
         if($query)
         {
               return back()->with('xong','ờ m vào được rồi');
         }
         else
         {
            return back()->with('loi','Có lỗi xảy ra vui long thử lại sau');
         }
         

    } 

    public function doLogin(Request $request)
    { 
    
      $request->validate([      
         'email'=>'required|email',
         'password'=>'required|min:5|max:18',
        ]);
      //nếu đầu vào hợp lệ thì cho đăng nhập
      //kiểm tra email đã tồn tại chưa
      $user= User::where('email','=',$request->email)->first();
      if($user)
      {  
         
         //nếu có tồn tại mail thì kiểm tra mật khẩu
         if(Hash::check($request->password,$user->pass))
         {  
            //nêu có thì lưu vào secction
            $request->session()->put('id',$user->userid);
            return redirect("/chat");
         }
         else
         {
            return redirect("/")->with("loi","User name or password incorrect");
         }
      }
      else
      {
             return redirect("/")->with("loi","User name or password incorrect");
      }
    }
   public function auth()
   { 
      $user= DB::table('Friend')
      ->Where(function($query) {
          $query->orWhere('fromid', session()->get('id'))
                ->orWhere('receiverid',  session()->get('id'))
                ->where('status', "1");
      })
      ->first();
     
      if($user)
      {
          $pusher=new Pusher("b708a607270d00087a1f","6386e28cedf126f8a592","1175239");
          return $pusher->socket_auth($_POST["channel_name"],$_POST["socket_id"]);
      }
      else
      {
         abort(403);
      }
     
   }
 
   public function updateUser(Request $request)
   {    $file="";
    if($request->avt) {
       
        $fileName = time().'_'.$request->avt->getClientOriginalName();
           
        $file=$request->file('avt')->storeAs('avataUser/'.session()->get('id'), $fileName, 'public');
    
     }
     
    $request->validate([
        'username'=>'required|max:20',
        'avt' => 'mimes:jpeg,bmp,png',
        'address'=>'max:100',
        'about'=>'max:500',
       ]);
       $query=DB::table('user')
       ->where('userid', session("id"))
       ->update(
        [
        
           'name' =>$request->username,
           'mota'=>$request->about,
           'diachi'=>$request->address
        ]
        );
            //nếu chọn ảnh
        if($file!="")
            {
               //xoá avt cũ trong sever
               $dataUser= User::where('userid','=',session()->get('id'))->first();
               Storage::disk('public')->delete($dataUser->avata);
               $query=DB::table('user')
               ->where('userid', session("id"))
               ->update(
                  [
                     'avata'=>$file
                  ]
                  );
        }
        if($query)
        {
            return redirect("chat")->with("xong","Update Complete");
        }
        
        
   }

   public function searchFriend(Request $request)
   {
           $result=[
               "ketqua"=>User::where('email','LIKE','%'.$request->key.'%')
                ->orWhere('name','LIKE','%'.$request->key.'%')
                ->get()
           ];
           return $result;
   }
//sửa
   public function RecomentUser()
   {
        $dataUser=[
            
            "recoment"=>User::inRandomOrder()->limit(10)->whereNotIn('userid', function ($query) {
                $query->select('receiverid')->from('Friend');
                })
            ->orWhereNotIn('userid', function ($query) {
                    $query->select('fromid')->from('Friend');
                })
            ->get()
        ];
        return $dataUser;
     
   }

   public function addFriend(Request $r)
   {
      
    $query=friends::insert(
        [
           'fromid'=>session()->get('id'),
           'receiverid'=> $r->id,
           'status'=>'0',
           'time'=>Carbon::now()->toDateString()
        ]
     );
   
   
   }


   public function getFriendRequest(Request $r)
   {
  
    $data=[
        "FriendRequest"=>User::whereIn('userid',function($query) {
                    $query->from('Friend')
                    ->where('fromid',session()->get('id'))
                    ->orWhere('receiverid',session()->get('id'))
                    ->Where('status','0')
                    ->select('fromid');
                })
        ->orWhereIn('userid',function($query) {
                    $query->from('Friend')
                    ->where('fromid',session()->get('id'))
                    ->orWhere('receiverid',session()->get('id'))
                    ->Where('status','0')
                    ->select('receiverid');
                })->where('userid','!=',session()->get('id'))
        ->get()
    ];
    return $data;
   }

   function answerFriendRequest(Request $r)
    {
       if(request()->answer==1)
       {
            friends::where(
            function($query) {
            $query->where('fromid',session()->get('id'))
            ->Where('receiverid',request()->id)
            ->Where('status','0');
            }
            )
            ->orWhere(
                function($query) {
                $query->where('fromid',request()->id)
                ->Where('receiverid',session()->get('id'))
                ->Where('status','0');
            }
            )
            ->update(['status'=>request()->answer]);
       }
       
        else{
            friends::where(
                function($query) {
                $query->where('fromid',session()->get('id'))
                ->Where('receiverid',request()->id)
                ->Where('status','0');
                }
                )
                ->orWhere(
                    function($query) {
                    $query->where('fromid',request()->id)
                    ->Where('receiverid',session()->get('id'))
                    ->Where('status','0');
                }
                )
                ->delete();
        }

    }

























//của thu viện k động vào
       
     function create($username)
    {
        $apiKeySid = 'SKQIJa3oKgIzE6WJnPrrLZXwfaBKyVKmuN';
        $apiKeySecret = "QjJaamE5a0lQMnNPdEhWdE45MmtHcXNCNDVtY3dTWTU=";

        $now = time();
        $exp = $now + 999999999;

        $username = $username;

        if(!$username){
            $jwt = '';
        }else {
            $header = array('cty' => "stringee-api;v=1");
            $payload = array(
                "jti" => $apiKeySid . "-" . $now,
                "iss" => $apiKeySid,
                "exp" => $exp,
                "icc_api" => true,
                "userId" => $username
            );

            $jwt = $this->encode($payload, $apiKeySecret, 'HS256', null, $header);
        }



        $res = array(
            'access_token' => $jwt
            );

        header('Access-Control-Allow-Origin: *');
        return $res["access_token"];
    }

/**
 * When checking nbf, iat or expiration times,
 * we want to provide some extra leeway time to
 * account for clock skew.
 */
public static $leeway = 0;

/**
 * Allow the current timestamp to be specified.
 * Useful for fixing a value within unit testing.
 *
 * Will default to PHP time() value if null.
 */
public static $timestamp = null;

public static $supported_algs = array(
    'HS256' => array('hash_hmac', 'SHA256'),
    'HS512' => array('hash_hmac', 'SHA512'),
    'HS384' => array('hash_hmac', 'SHA384'),
    'RS256' => array('openssl', 'SHA256'),
);


public static function decode($jwt, $key, $allowed_algs = array())
{
    $timestamp = is_null(static::$timestamp) ? time() : static::$timestamp;

    if (empty($key)) {
        throw new InvalidArgumentException('Key may not be empty');
    }
    if (!is_array($allowed_algs)) {
        throw new InvalidArgumentException('Algorithm not allowed');
    }
    $tks = explode('.', $jwt);
    if (count($tks) != 3) {
        throw new UnexpectedValueException('Wrong number of segments');
    }
    list($headb64, $bodyb64, $cryptob64) = $tks;
    if (null === ($header = static::jsonDecode(static::urlsafeB64Decode($headb64)))) {
        throw new UnexpectedValueException('Invalid header encoding');
    }
    if (null === $payload = static::jsonDecode(static::urlsafeB64Decode($bodyb64))) {
        throw new UnexpectedValueException('Invalid claims encoding');
    }
    $sig = static::urlsafeB64Decode($cryptob64);
    
    if (empty($header->alg)) {
        throw new UnexpectedValueException('Empty algorithm');
    }
    if (empty(static::$supported_algs[$header->alg])) {
        throw new UnexpectedValueException('Algorithm not supported');
    }
    if (!in_array($header->alg, $allowed_algs)) {
        throw new UnexpectedValueException('Algorithm not allowed');
    }
    if (is_array($key) || $key instanceof \ArrayAccess) {
        if (isset($header->kid)) {
            $key = $key[$header->kid];
        } else {
            throw new UnexpectedValueException('"kid" empty, unable to lookup correct key');
        }
    }

   

    return $payload;
}


 function encode($payload, $key, $alg = 'HS256', $keyId = null, $head = null)
{
    $header = array('typ' => 'JWT', 'alg' => $alg);
    if ($keyId !== null) {
        $header['kid'] = $keyId;
    }
    if ( isset($head) && is_array($head) ) {
        $header = array_merge($head, $header);
    }
    $segments = array();
    $segments[] = static::urlsafeB64Encode(static::jsonEncode($header));
    $segments[] = static::urlsafeB64Encode(static::jsonEncode($payload));
    $signing_input = implode('.', $segments);

    $signature = static::sign($signing_input, $key, $alg);
    $segments[] = static::urlsafeB64Encode($signature);

    return implode('.', $segments);
}


public static function sign($msg, $key, $alg = 'HS256')
{
    if (empty(static::$supported_algs[$alg])) {
        throw new DomainException('Algorithm not supported');
    }
    list($function, $algorithm) = static::$supported_algs[$alg];
    switch($function) {
        case 'hash_hmac':
            return hash_hmac($algorithm, $msg, $key, true);
        case 'openssl':
            $signature = '';
            $success = openssl_sign($msg, $signature, $key, $algorithm);
            if (!$success) {
                throw new DomainException("OpenSSL unable to sign data");
            } else {
                return $signature;
            }
    }
}


private static function verify($msg, $signature, $key, $alg)
{
    if (empty(static::$supported_algs[$alg])) {
        throw new DomainException('Algorithm not supported');
    }

    list($function, $algorithm) = static::$supported_algs[$alg];
    switch($function) {
        case 'openssl':
            $success = openssl_verify($msg, $signature, $key, $algorithm);
            if (!$success) {
                throw new DomainException("OpenSSL unable to verify data: " . openssl_error_string());
            } else {
                return $signature;
            }
        case 'hash_hmac':
        default:
            $hash = hash_hmac($algorithm, $msg, $key, true);
            if (function_exists('hash_equals')) {
                return hash_equals($signature, $hash);
            }
            $len = min(static::safeStrlen($signature), static::safeStrlen($hash));

            $status = 0;
            for ($i = 0; $i < $len; $i++) {
                $status |= (ord($signature[$i]) ^ ord($hash[$i]));
            }
            $status |= (static::safeStrlen($signature) ^ static::safeStrlen($hash));

            return ($status === 0);
    }
}


public static function jsonDecode($input)
{
    if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
        /** In PHP >=5.4.0, json_decode() accepts an options parameter, that allows you
         * to specify that large ints (like Steam Transaction IDs) should be treated as
         * strings, rather than the PHP default behaviour of converting them to floats.
         */
        $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
    } else {
        /** Not all servers will support that, however, so for older versions we must
         * manually detect large ints in the JSON string and quote them (thus converting
         *them to strings) before decoding, hence the preg_replace() call.
         */
        $max_int_length = strlen((string) PHP_INT_MAX) - 1;
        $json_without_bigints = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);
        $obj = json_decode($json_without_bigints);
    }

    if (function_exists('json_last_error') && $errno = json_last_error()) {
        static::handleJsonError($errno);
    } elseif ($obj === null && $input !== 'null') {
        throw new DomainException('Null result with non-null input');
    }
    return $obj;
}


public static function jsonEncode($input)
{
    $json = json_encode($input);
    if (function_exists('json_last_error') && $errno = json_last_error()) {
        static::handleJsonError($errno);
    } elseif ($json === 'null' && $input !== null) {
        throw new DomainException('Null result with non-null input');
    }
    return $json;
}


public static function urlsafeB64Decode($input)
{
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $input .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}


public static function urlsafeB64Encode($input)
{
    return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
}


private static function handleJsonError($errno)
{
    $messages = array(
        JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
        JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
        JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
    );
    throw new DomainException(
        isset($messages[$errno])
        ? $messages[$errno]
        : 'Unknown JSON error: ' . $errno
    );
}

/**
 * Get the number of bytes in cryptographic strings.
 *
 * @param string
 *
 * @return int
 */
private static function safeStrlen($str)
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($str, '8bit');
    }
    return strlen($str);
}
}


    
