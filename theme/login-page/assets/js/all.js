function convert_blob(data) {
    var url = window.URL || window.webkitURL;
    var blob = url.createObjectURL(data);
    //console.log(data.size);
    return blob;
}

// INDEXED DB

var db;
var idb = {};
var db_promise = $.Deferred();
console.log('Baza ishga tushmoqda...');
window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction;
window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;
  
if (window.indexedDB) {
  var DBOpenRequest = window.indexedDB.open("bukharaeducation", 4);
    
  DBOpenRequest.onerror = function(event) {
    db_promise.resolve();
    db = "error_connect";
    console.log('Bazaga ulanishda xatolik yuzaga keldi.');
  };

  DBOpenRequest.onsuccess = function(event) {
    db = event.target.result;
    db_promise.resolve();
    console.log('Baza foydalanish uchun tayyor');
    // db = DBOpenRequest.result;
    if (db.objectStoreNames.length != 1) {
      confirm('dasturni o`chirib tashlab qayta o`rnating');
      db = "error_connect";
    }
  };

  DBOpenRequest.onupgradeneeded = function(event) {
    var db = event.target.result;
    db.onerror = function(event) {
      db = "error_connect";
      console.log('Bazaga ulanishda xatolik yuzaga keldi.');
    };
    
    db.createObjectStore("files");
    console.log('Tablitsa yaratildi.');
  };

  idb = {
    addItem: function(key, value, table) {
      if (db == "error_connect") return "error_connect";
      return new Promise(function(resolve){
        db_promise.then(function(){
          var transaction = db.transaction([table], "readwrite");
          transaction.oncomplete = function() {
            resolve('Ma`lumot bazaga saqlandi.');
          };
          transaction.onerror = function() {
            resolve('Ma`lumot bazaga saqlanmadi: ' + transaction.error);
          };
          var objectStore = transaction.objectStore(table);
          var objectStoreRequest = objectStore.add(value, key);
          objectStoreRequest.onsuccess = function(event) {
            // console.info('So`rov bajarildi.');
          };
        });
      });
    },
    
    getItem: function(key, table) {
      if (db == "error_connect") return key;
      return new Promise(function(resolve, reject) {
        db_promise.then(function(){
          var result;
          var transaction = db.transaction([table], "readonly");
          transaction.oncomplete = function(){resolve(result)}
          transaction.onerror = function(event){reject(event.target.error)}
          var objectStore = transaction.objectStore(table);
          var request = objectStore.get(key);
          request.onsuccess = function(){ result = request.result}
        });
      });
    },

    deleteItem: function(key, table) {
      if (db == "error_connect") return "error_connect";
      idb.getItem(key).then(function(res){
        if (res){
          return new Promise(function(resolve, reject) {
            db_promise.then(function(){
              var transaction = db.transaction([table], "readwrite");
              transaction.oncomplete = function(){resolve('deleted')}
              transaction.onerror = function(event){reject(event.target.error)}
              var objectStore = transaction.objectStore(table);
              var request = objectStore.delete(key);
              request.onsuccess = function(){resolve()}
            });
          });
        } else {
          console.log('not found');
        }
      });
    },

    getAll: function(table) {
      if (db == "error_connect") return "error_connect";
      return new Promise(function(resolve, reject) {
        db_promise.then(function(){
          var result;
          var transaction = db.transaction([table], "readonly");
          transaction.oncomplete = function(){resolve(result)}
          transaction.onerror = function(event){reject(event.target.error)}
          var objectStore = transaction.objectStore(table);
          var request = objectStore.getAll();
          request.onsuccess = function(){ result = request.result}
        });
      });
    },

  };
}

// 

$("#phone_input").on('input keyup', function(e){
  var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
  console.log(x);
  e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
});

$("#phone_input").keyup();

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()

$(document).on("click", "*[change-url]", function(){
  window.history.pushState({}, null, $(this).attr("change-url"));
});

window.addEventListener('popstate', function(){
  window.location.reload();
});



function downloadAudioFile(url) {
    return new Promise(function(resolve){
        var req = new XMLHttpRequest();
        req.open("GET", url, true);
        req.responseType = "blob";
        req.onload = function (event) {
            var blob = req.response;
            var file = convert_blob(blob)
            idb.addItem(url, blob, 'files').then(function(res){
                console.log(res);
            });
            resolve(file);
        };
        req.send();
    });
}
  
function sound(src){
    if ($(document).find("*[src='"+src+"']").length == 0){
        if (idb.addItem) {
            idb.getItem(src, 'files').then(function(data){
                if (!data){
                    downloadAudioFile(src).then(function(blob){
                        var new_sound = document.createElement("audio");
                        new_sound.src = blob;
                        new_sound.volume = 0.3;
                        new_sound.play();
                    });
                } else {
                    var new_sound = document.createElement("audio");
                    new_sound.src = convert_blob(data);
                    new_sound.volume = 0.3;
                    new_sound.play();
                }
            });
        }
    }
}

$(document).on("click", "*[like-course]", function(){
    sound("audios/5.mp3");
    var course_id = $(this).attr("like-course");
    var elm = $(this);
    $(elm).removeAttr("like-course");
  
    $.ajax({
        url: "/api",
        type: "POST",
        data: {
            "method": "likeCourse",
            "course_id": course_id,
        },
        success: function(data) {
            if (data == "OK") {
                $(elm).find(".fa").removeClass("fa-heart");
                $(elm).find(".fa").addClass("fa-heart-o");
        
                setTimeout(function(){
                    $(elm).attr("unlike-course", course_id);
                }, 1000);
            }
        }
    })
});

$(document).on("click", "*[unlike-course]", function(){
    sound("audios/5.mp3");
    var course_id = $(this).attr("unlike-course");
    var elm = $(this);
    $(elm).removeAttr("unlike-course");

    $.ajax({
        url: "/api",
        type: "POST",
        data: {
            "method": "likeCourse",
            "course_id": course_id,
        },
        success: function(data) {
            if (data == "OK") {
                $(elm).find(".fa").removeClass("fa-heart-o");
                $(elm).find(".fa").addClass("fa-heart");

                setTimeout(function(){
                $(elm).attr("like-course", course_id);
                }, 1000);
            }
        }
    })
});

$(document).on("click", "*[data-bs-dismiss='modal']", function(){
  video_player.pause();
  audio_player.pause();
});