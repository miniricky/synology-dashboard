/**
 * @file
 * Add functionality for Packages information.
 */

(function ($) {
  document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname === '/') {
      const data = {
        info: 'packages'
      };
      const xhr = new XMLHttpRequest();
      xhr.open('POST', '../includes/packages-info.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            try {
              const responseData = JSON.parse(xhr.responseText);
              const container = document.querySelector('.package-wrapper')

              responseData.packages.forEach(pkg => {
                const packageDiv = document.createElement('div');
                packageDiv.classList.add('package');
                packageDiv.classList.add(pkg.id);

                const imageDiv = addImage(pkg.name, pkg.id);
                const idDiv = addSpan(pkg.name);

                packageDiv.appendChild(imageDiv);
                packageDiv.appendChild(idDiv);

                container.appendChild(packageDiv);
              });
            } catch (e) {
              console.log(e + xhr.responseText);
            }
          } else {
            console.log('Error in AJAX request');
          }
        }
      }

      const params = `info=${encodeURIComponent(data.info)}`;
      xhr.send(params);
      /*
      * Function for add image to markup.
      */
      function addImage(name, id) {
        const packages = ['DownloadStation'];

        if (packages.includes(id)) {
          const anchor = document.createElement('a');
          anchor.setAttribute('data-bs-toggle', 'modal');
          anchor.setAttribute('data-bs-target', '#' + id);

          const imageDiv = document.createElement('div');
          imageDiv.classList.add('image');
          anchor.appendChild(imageDiv);

          const src = name.replace(/\s+/g, '-').toLowerCase();
          const img = document.createElement('img');
          img.setAttribute('src', '../images/synology-packages/' + src + '.png');
          img.setAttribute('alt', name + ' logo');
          imageDiv.appendChild(img);

          return anchor;
        } else {
          const imageDiv = document.createElement('div');
          imageDiv.classList.add('image');

          const src = name.replace(/\s+/g, '-').toLowerCase();
          const img = document.createElement('img');
          img.setAttribute('src', '../images/synology-packages/' + src + '.png');
          img.setAttribute('alt', name + ' logo');
          imageDiv.appendChild(img);

          return imageDiv;
        }
      }

      /*
      * Function for add span to container.
      */
      function addSpan(name) {
        const idDiv = document.createElement('div');
        idDiv.classList.add('text');
        const nameSpan = document.createElement('span');
        nameSpan.textContent = name;
        idDiv.appendChild(nameSpan);

        return idDiv;
      }
    }
  });
})(jQuery);