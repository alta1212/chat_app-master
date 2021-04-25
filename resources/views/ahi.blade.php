<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script>


//file này để test k quan trọng
    var pusher = new Pusher('b708a607270d00087a1f', {
      cluster: 'ap3',
      authEndpoint: '/auth',
      auth: {headers: {'X-CSRF-Token': "{{ csrf_token() }}"}}
    });


    
  </script>
</head>
<body>

@foreach($friend as $r)
  {{$r->name}}
@endforeach
 <form action="sendMes" method="post">
 @csrf
 <input type="hidden"  name="chanel" value="1-3">
        <input type="text"  name="data">
        <input type="submit" value="gửi">
 </form>
   <script>
  @foreach($friend as $r)
 

  var cn{{$r->chanel}} = pusher.subscribe('private-private-{{$r->chanel}}');
    
  cn{{$r->chanel}}.bind('test', function(data) {
        console.log(data);
    });
    

 
 @endforeach
 </script>
</body>