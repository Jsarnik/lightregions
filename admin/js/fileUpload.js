var uploadSuccess = false;
var uploadPath = '';
var og_path = '';
var errorMsg = '';

var myVarWatch = (function() {
    var watches = {};

    return {
        watch: function(callback) {
            var id = Math.random().toString();
            watches[id] = callback;

            // Return a function that removes the listener
            return function() {
                watches[id] = null;
                delete watches[id];
            }
        },
        trigger: function() {
            for (var k in watches) {
                watches[k](window.uploadSuccess);
            }
        }
    }
})();

var myVarWatch2 = (function() {
    var watches = {};

    return {
        watch: function(callback) {
            var id = Math.random().toString();
            watches[id] = callback;

            // Return a function that removes the listener
            return function() {
                watches[id] = null;
                delete watches[id];
            }
        },
        trigger: function() {
            for (var k in watches) {
                watches[k](window.errorMsg);
            }
        }
    }
})();

$(document).ready(function(){
// 
// 
})

function progressBarStart(){
    $('#upload_frame').show(); 
    function set () { 
        $('#upload_frame').attr('src','upload_frame.php?up_id=<?php echo $up_id; ?>'); 
    } 
    setTimeout(set); 
}

function CopyMe(oFileInput, sTargetID) {
    $('#' + sTargetID).focus();
    var sArray = oFileInput.value.split("\\");
    var filename = sArray[sArray.length-1];

    if(filename === 'undefined' || filename === null || filename ==='')
        return;

    uploadPath = $('#destFolder').val() + filename;

    document.getElementById(sTargetID).value = uploadPath;
    document.getElementById('my_form').submit();

    progressBarStart();
}

function IsSuccess(uploadGood){
  uploadSuccess = uploadGood;

  if(!uploadSuccess){
    $('#upload_frame').hide();
    error('file failed to upload');
  }
  else
    error('');

  myVarWatch.trigger();
}

function error(message){    
    errorMsg = message;
    myVarWatch2.trigger();
}

function informMe(msg){
    console.log(msg);
}