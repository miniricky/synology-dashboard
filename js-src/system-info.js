/**
 * @file
 * Add functionality for System information.
 */

(function ($) {
  document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname === '/') {
      const sessionCookie = getCookie('sid');
      if (sessionCookie) {
        const elemento = document.querySelector('body');
        elemento.classList.remove('visually-hidden');
      }
      else{
        window.location.href = 'login.html';
      }

      systeInfo();
      ramInfo();
      hddInfo();

      /*
      * Function for create XHR.
      */
      function createXHR(url, params, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              try {
                const responseData = JSON.parse(xhr.responseText);
                callback(null, responseData);
              } catch (e) {
                callback(e, null);
              }
            } else {
              callback(new Error('Error in AJAX request: ' + xhr.statusText), null);
              console.log('Error in AJAX request: ' + xhr.statusText);
            }
          }
        };
        xhr.send(params);
      }

      /*
      * Function for get system info.
      */
      function systeInfo() {
        const data = `info=system-info`;
        createXHR('../includes/system-info.php', data, function (err, responseData) {
          if (responseData.system.status == 'true') {
            const model = document.querySelector('.model-info');
            if (responseData.system.model) {
              model.textContent = responseData.system.model;
            }

            const processor = document.querySelector('.processor-info')
            if (responseData.system.processor) {
              processor.textContent = responseData.system.processor;
            }

            const cores = document.querySelector('.cores-info')
            if (responseData.system.cores) {
              cores.textContent = responseData.system.cores;
            }

            const firmware = document.querySelector('.firmware-info')
            if (responseData.system.firmware) {
              firmware.textContent = responseData.system.firmware;
            }
          }
          else {
            console.log(responseData.system.error);
          }
        });
      }

      /*
      * Function for get ram info.
      */
      function ramInfo() {
        const data = `info=ram-info`;
        createXHR('../includes/system-info.php', data, function (err, responseData) {
          if (responseData.ram.status == 'true') {
            const ramTotal = document.querySelector('.ram-info')
            if (responseData.ram.ramTotal) {
              ramTotal.textContent = responseData.ram.ramTotal + 'GB';
            }

            const ramUsed = document.querySelector('.ram .progress-bar')
            if (responseData.ram.ramUsed) {
              ramUsed.textContent = responseData.ram.ramUsed + '%';

              if (responseData.ram.ramUsed > 15) {
                ramUsed.style.width = responseData.ram.ramUsed + '%';
              }
              else {
                ramUsed.style.width = (responseData.ram.ramUsed * 2.5) + '%';
              }
            }
          }
          else {
            console.log(responseData.ram.error);
          }
        });
      }

      /*
      * Function for get hdd info.
      */
      function hddInfo() {
        const data = `info=hdd-info`;
        createXHR('../includes/system-info.php', data, function (err, responseData) {
          const hddTotal = document.querySelector('.hdd-info')
          if (responseData.hdd.hddTotal) {
            hddTotal.textContent = responseData.hdd.hddTotal + 'TB';
          }

          const hddUsed = document.querySelector('.hdd .progress-bar')
          if (responseData.hdd.hddUsed) {
            hddUsed.textContent = responseData.hdd.hddUsed + 'TB';
            var hddWidth = (responseData.hdd.hddTotal * 100) / (responseData.hdd.hddUsed * 100);

            if (hddWidth > 15) {
              hddUsed.style.width = hddWidth + '%';
            }
            else {
              hddUsed.style.width = (hddWidth * 2) + '%';
            }
          }
        });
      }

      /*
      * Function for get cookie.
      */
      function getCookie(sid) {
        const sidEQ = sid + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
          let c = ca[i].trim();
          if (c.indexOf(sidEQ) == 0) {
            return c.substring(sidEQ.length, c.length);
          }
        }
        return null;
      }
    }
  });
})(jQuery);