/**
 * @file
 * Add functionality for verify SID.
 */

(function ($) {
  document.addEventListener('DOMContentLoaded', function() {
    const sid = getCookie('sid');
    const domain = getCookie('domain');

    if (sid) {
      validateSID(sid, domain);
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

    function deleteCookie(name) {
      setCookie(name, "", 1);
    }

    /*
      * Function for making POST requests.
      */
    async function fetchData(url, params) {
      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: params
        });

        if (!response.ok) {
          throw new Error('Error in AJAX request: ' + response.statusText);
        }

        const responseData = await response.json();
        return responseData;
      } catch (error) {
        console.error('Error:', error);
        throw error;
      }
    }

    /*
      * Function to validate SID.
      */
    async function validateSID(sid, domain) {
      const data = `info=validate-sid&sid=${encodeURIComponent(sid)}&domain=${encodeURIComponent(domain)}`;
      try {
        const responseData = await fetchData('../includes/login.php', data);
        if (responseData.validate.status === 'false') {
          deleteCookie('sid');
          deleteCookie('domain');
          window.location.href = 'login.html';
        }
      } catch (error) {
        console.error('Validation error:', error);
      }
    }
  });
})(jQuery);