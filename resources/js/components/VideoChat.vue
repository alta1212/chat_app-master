<template>
    <div id="video-chat-window" class="videocall call-modal">
                        <!-- <video class="videocall call-modal bg-size" width="" height="" controls="">
                            <source src="https://www.w3schools.com/tags/movie.mp4" type="video/mp4">
                            <source src="https://www.w3schools.com/tags/movie.ogg" type="video/ogg">
                        </video> -->
                      
                    <div id="myVideoCall" class="small-image">
                        
                    </div>
    </div>
</template>

<script>
export default {
    name: 'video-chat',
    data: function () {
        return {
            accessToken: ''
        }
    },
    methods : {
        getAccessToken : function () {

            const _this = this
            const axios = require('axios')
            
            // Request a new token
            axios.get('/access_token')
                .then(function (response) {
                    _this.accessToken = response.data
                })
                .catch(function (error) {
                    console.log(error);
                })
                .then(function () {
                    _this.connectToRoom()
                });
        },
        connectToRoom : async function () {

            const _this = this
            const { connect, createLocalVideoTrack } = require('twilio-video');
            
            // Join to the Room with the given AccessToken and ConnectOptions.
            const room = await connect(this.accessToken, {audio: false, video: { width: 640, height: 640 } });
            console.log(room)
            // Make the Room available in the JavaScript console for debugging.
            window.room = room;
             var o=0;
            const videoChatWindow = document.getElementById('video-chat-window');
            const myVideo=document.getElementById('myVideoCall');
            this.addLocalParticipant(room.localParticipant,o)
            console.log(room.localParticipant)
           
            // Subscribe to the media published by RemoteParticipants already in the Room.
            room.participants.forEach(participant => {this.addRemoteParticipant(participant);});
            room.on('participantConnected', participant => {
                    console.log(`Participant "${participant.identity}" connected`);

                    // participant.tracks.forEach(publication => {
                    //     if (publication.isSubscribed) {
                    //         const track = publication.track;
                    //         videoChatWindow.appendChild(track.attach()); 
                    //        console.log(track.attach())
                    //     }
                    // });
                  
                    participant.on('trackSubscribed', track => {
                        track.attach().classList.add("small-image","bg-size");
                        document.getElementById('myVideoCall').appendChild(track.attach());
                        // videoChatWindow.appendChild(track.attach());
                        //  console.log(track.attach())
                    });
                   
                });
                
               
        },
        addLocalParticipant: function(participant,o) {
          
            // // Create the video container
            // this.createVideoContainer(participant,o)

            // Attach the 
            participant.tracks.forEach(publication => {
                
                if ('audio' == publication.kind)
                    return

                this.publishTrack(publication.track, participant)
            })
        },
        addRemoteParticipant: function(participant) {
         
            // this.createVideoContainer(participant)

            // Set up listener to monitor when a track is published and ready for use
            participant.on('trackSubscribed', track => {
                this.publishTrack(track, participant);
            });
        },
        createVideoContainer: function (participant,o) {
            
            // Add a container for the Participant's media.
             console.log(o);
            if(o==0)
            {   const div = document.createElement('div');
                div.id = participant.sid;
                div.classList.add("videocall","call-modal","bg-size");
                document.getElementById('video-chat-window').appendChild(div);
            }
            else
            {   const div = document.createElement('div');
                div.id = participant.sid;
                div.classList.add("small-image","bg-size");
                document.getElementById('myVideoCall').appendChild(div);
            }
           

           
        },
        publishTrack: ( track, participant ) => {
            const videoContainer = document.getElementById('myVideoCall');
            track.attach().classList.add("call-modal","videocall","bg-size")
            videoContainer.appendChild(track.attach())
        }
    },
    mounted : function () {
        this.getAccessToken()
    }
}

</script>