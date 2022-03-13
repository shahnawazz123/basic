<?php

use yii\helpers\Html;
//use yii\widgets\DetailView;
use kartik\detail\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\DoctorAppointments */

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
// $this->registerJsFile(BaseUrl::home() . 'twilio.css', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $model->name.' - Room Name : '.$roomName;
$this->params['breadcrumbs'][] = ['label' => 'Doctor Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view?id='.$model->doctor_appointment_id]];
\yii\web\YiiAsset::register($this);
$model->is_call_initiated = 1;
$model->save(false);
?>
<style>
.docLogo{
    height: 90px;
  position: absolute;
  width: 90px;
  left: 28%;
  top: 28%;
}
#remote-media {
  display: flex;
  align-items: stretch;
  justify-content: stretch;
    width:100%;
    height: 594px;
    background: #333;
}
#remote-media video {
    width:100%;
    height: 100%;
}
#room-controls{
    bottom: 23px;
    position: absolute;
}
#local-media{
      position: absolute;
  top: 40px;
  right: 40px;
  z-index: 10;
  width: 200px;
  height: 200px;
  background: #000;
  border-radius: 10px;
}
#local-media video{
    display: block;
  width: 100%;
  height: 100%;
}

#button-mute-on,#button-startvideo{
    display: none;
}

</style>
<script src="//media.twiliocdn.com/sdk/js/video/v1/twilio-video.min.js"></script>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <div class="row">
                        <div  class="col-md-6">
                            <center><h4 id="usrJoin"></h4></center>
                            <!-- <div id="remote-media"></div> -->
                        </div>
                        <!-- <div id="controls" class="col-md-6">
                                <center><h4 id="drJoin"></h4></center>
                            <div id="preview">
                                <div id="local-media"  style="float: left;"></div>
                                <br>
                            </div>
                        </div> -->
                        <button id="join-room-preview" class="btn btn-warning">Join Room</button>
                        <!-- <div id="room-controls" class="col-md-12">
                            <hr>
                            <center>
                                <button id="join-room-preview" class="btn btn-warning">Join Room</button>
                                <button id="button-preview" class="btn btn-primary">Preview My Camera</button>
                                <button id="button-join" class="btn btn-info">Join Room</button>
                                <button id="button-leave" class="btn btn-danger">Leave Room</button>
                            </center><br><br>
                        </div> -->
                        <div id="log" class="col-md-12" style="padding: 10px;"></div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div id="VideoCall" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width: 90%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body">
        <div id="remote-media"></div>
        <div id="local-media">
            <?php $doctor_image = (isset($model->doctor) && !empty($model->doctor->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->doctor->image) : Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');?>
            <img src="<?=$doctor_image;?>" class="img-circle docLogo" id="docLogo">
        </div>
      </div>
      <div class="modal-footer">
        <div id="room-controls" class="col-md-12">
            <center>
                <button id="button-mute-off" class="btn btn-info" title="Mute"><i class="fa fa-microphone"></i> </button>
                <button id="button-mute-on" class="btn btn-info" title="Unmute"><i class="fa fa-microphone-slash"></i> </button>
                <button id="button-leave" class="btn btn-danger" title="Leave Room"><i class="fa fa-phone"></i></button>
                <button id="button-stopvideo" class="btn btn-primary" title="Preview My Camera"><i class="fa fa-video-camera"></i></button>
                <button id="button-startvideo" class="btn btn-primary" title="Preview My Camera"><i class="fa fa-play"></i></button>
                <button id="button-join" hidden class="btn btn-info">Join Room</button>
            </center><br><br>
        </div>
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
      </div>
    </div>

  </div>
</div>
    

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
  <script src="//media.twiliocdn.com/sdk/js/video/v1/twilio-video.min.js"></script>
  <script type="text/javascript">
    var activeRoom;
    var previewTracks;
    var identity;
    var roomName;
    //joinRoom(<?php //$roomName;?>);
console.log("<?php echo $token; ?>");
    $("#join-room-preview").click(function()
    {
        callInitaited(1); 
        /*joinRoom(<?=$roomName;?>);
        $("#VideoCall").modal('show');*/
    });

    
    // Attach the Tracks to the DOM.
    function attachTracks(tracks, container) {
      tracks.forEach(function(track) {
        container.appendChild(track.attach());
      });
    }

    // Attach the Participant's Tracks to the DOM.
    function attachParticipantTracks(participant, container) {
      var tracks = Array.from(participant.tracks.values());
      attachTracks(tracks, container);
    }

    // Detach the Tracks from the DOM.
    function detachTracks(tracks) {
      tracks.forEach(function(track) {
        track.detach().forEach(function(detachedElement) {
          detachedElement.remove();
        });
      });
    }

    // Detach the Participant's Tracks from the DOM.
    function detachParticipantTracks(participant) {
      var tracks = Array.from(participant.tracks.values());
      detachTracks(tracks);
    }

    // When we are about to transition away from this page, disconnect
    // from the room, if joined.
    window.addEventListener('beforeunload', leaveRoomIfJoined);


    identity = "<?php echo $roomName; ?>";
    document.getElementById('room-controls').style.display = 'block';

    // Bind button to join Room.
    document.getElementById('button-join').onclick = function() {
      roomName = ("<?php echo $roomName; ?>");
      
      joinRoom(roomName);
    };

    // Bind button to leave Room.
    document.getElementById('button-leave').onclick = function() {
      log('Leaving room...');
      activeRoom.disconnect();
      $("#drJoin").text("");
      $("#usrJoin").text("");
      $("#VideoCall").modal('hide');
      callInitaited(2);
    };

    function callInitaited(act)
    {
      document.getElementById('docLogo').style.display = 'inline';
        $(".global-loader").show();
        $.ajax({
            type: "GET",
            url: baseUrl+'doctor-appointment/doctor-call-initaited',
            data:{
                "id":'<?=$model->doctor_appointment_id;?>',
                "act":act,
            },
            success: function(res)
            {
                $(".global-loader").hide();
                if(res==1)
                {
                  joinRoom(<?=$roomName;?>);
                  $("#VideoCall").modal('show');
                  document.getElementById('docLogo').style.display = 'none';
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".global-loader").hide();
                alert(jqXHR.responseText);
            }
        });
    }


    // Successfully connected!
    function roomJoined(room) {
      window.room = activeRoom = room;

      log("Joined as '" + identity + "'");
      document.getElementById('button-join').style.display = 'none';
      document.getElementById('button-leave').style.display = 'inline';

      // Attach LocalParticipant's Tracks, if not already attached.
      var previewContainer = document.getElementById('local-media');
      if (!previewContainer.querySelector('video')) {
        attachParticipantTracks(room.localParticipant, previewContainer);
      }



      // Attach the Tracks of the Room's Participants.
      room.participants.forEach(function(participant) {
        log("Already in Room: '" + participant.identity + "'");
        var previewContainer = document.getElementById('remote-media');
        attachParticipantTracks(participant, previewContainer);
        $("#usrJoin").text("User");
      });

      // When a Participant joins the Room, log the event.
      room.on('participantConnected', function(participant) {
        log("Joining: '" + participant.identity + "'");
      });

      // When a Participant adds a Track, attach it to the DOM.
      room.on('trackAdded', function(track, participant) {
        log(participant.identity + " added track: " + track.kind);
        var previewContainer = document.getElementById('remote-media');
        attachTracks([track], previewContainer);
      });

      // When a Participant removes a Track, detach it from the DOM.
      room.on('trackRemoved', function(track, participant) {
        log(participant.identity + " removed track: " + track.kind);
        detachTracks([track]);
      });

      // When a Participant leaves the Room, detach its Tracks.
      room.on('participantDisconnected', function(participant) {
        log("Participant '" + participant.identity + "' left the room");
        detachParticipantTracks(participant);
      });

      // Once the LocalParticipant leaves the room, detach the Tracks
      // of all Participants, including that of the LocalParticipant.
      room.on('disconnected', function() {
        log('Left');
        if (previewTracks) {
          previewTracks.forEach(function(track) {
            track.stop();
          });
        }
        detachParticipantTracks(room.localParticipant);
        room.participants.forEach(detachParticipantTracks);
        activeRoom = null;
        document.getElementById('button-join').style.display = 'inline';
        document.getElementById('button-leave').style.display = 'none';
      });
    }

    document.getElementById('button-mute-off').onclick = function() {
      room.localParticipant.audioTracks.forEach(track => {
        track.disable();
        document.getElementById('button-mute-on').style.display = 'inline';
        document.getElementById('button-mute-off').style.display = 'none';
      });
    }

    document.getElementById('button-mute-on').onclick = function() {
      room.localParticipant.audioTracks.forEach(track => {
        track.enable();
        document.getElementById('button-mute-off').style.display = 'inline';
        document.getElementById('button-mute-on').style.display = 'none';
      });
    }

    /*$(document).on("click", "#button-mute-on" , function() {    
        room.localParticipant.videoTracks.forEach(track => {
        track.enable();
        $("#button-mute-off").css('display','block');
        $(this).css('display','none');
        $(this).html('<i class="fa fa-microphone"></i>');
        });
    });*/

    /*document.getElementById('button-mute-on').onclick = function() {
      room.localParticipant.audioTracks.forEach(track => {
        track.enable();
        $(this).attr('id','button-mute-off');
      });
    }*/

    document.getElementById('button-stopvideo').onclick = function() {
        room.localParticipant.videoTracks.forEach(track => {
        track.disable();
            document.getElementById('button-startvideo').style.display = 'inline';
          document.getElementById('button-stopvideo').style.display = 'none';
          document.getElementById('docLogo').style.display = 'inline';
        });
    }

    document.getElementById('button-startvideo').onclick = function() {
        room.localParticipant.videoTracks.forEach(track => {
        track.enable();
            document.getElementById('button-stopvideo').style.display = 'inline';
        document.getElementById('button-startvideo').style.display = 'none';
        document.getElementById('docLogo').style.display = 'none';
        });
    }


    // Preview LocalParticipant's Tracks.
    document.getElementById('button-preview').onclick = function() {
      var localTracksPromise = previewTracks ?
        Promise.resolve(previewTracks) :
        Twilio.Video.createLocalTracks();

      localTracksPromise.then(function(tracks) {
        window.previewTracks = previewTracks = tracks;
        var previewContainer = document.getElementById('local-media');
        if (!previewContainer.querySelector('video')) {
          attachTracks(tracks, previewContainer);
        }
      }, function(error) {
        console.error('Unable to access local media', error);
        log('Unable to access Camera and Microphone');
      });
    };

    // Activity log.
    function log(message) {
      var logDiv = document.getElementById('log');
      logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
      logDiv.scrollTop = logDiv.scrollHeight;
    }

    // Leave Room.
    function leaveRoomIfJoined() {
      if (activeRoom) {
        activeRoom.disconnect();
      }
    }

    function joinRoom(roomName)
    {
        if (!roomName) {
        alert('Please enter a room name.');
        return;
      }

      log("Joining room '" + roomName + "'...");
      var connectOptions = {
        name: roomName,
        logLevel: 'debug'
      };

      if (previewTracks) {
        connectOptions.tracks = previewTracks;
      }

      // Join the Room with the token from the server and the
      // LocalParticipant's Tracks.
      Twilio.Video.connect("<?php echo $token; ?>", connectOptions).then(roomJoined, function(error) {
        log('Could not connect to Twilio: ' + error.message);
      });
    }
  </script>
</body>

</html>