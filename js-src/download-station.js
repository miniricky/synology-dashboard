/**
 * @file
 * Add functionality for download.
 */

(function ($) {
  document.addEventListener('DOMContentLoaded', function () {
    if (window.location.pathname === '/') {
      const modal = document.querySelector('#DownloadStation');
      const urlForm = modal.querySelector('#url-form');

      urlForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const url = modal.querySelector('#url-form #animeUrl').value;
        const encodedUrl = encodeURIComponent(url);
        sendURL(encodedUrl);
      });

      const scraping = modal.querySelector('#initScraping');
      scraping.addEventListener('click', function () {
        scrapingPagination(modal);
      });

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
                console.log(e + null);
              }
            } else {
              callback(new Error('Error in AJAX request: ' + xhr.statusText), null);
              console.log(xhr.statusText);
            }
          }
        };
        xhr.send(params);
      }

      /*
      * Function for scraping pagination.
      */
      function scrapingPagination(modal) {
        const data = `info=scraping-pagination`;
        createXHR('../includes/download-station.php', data, function (err, responseData) {
          createPagination(modal, responseData.pagination.pages)
          addSearch(modal);
          scrapingFLV(modal, '1');
        });
      }

      /*
      * Function for create pagination.
      */
      let currentPage = 1;
      const visibleButtons = 11;
      const sideButtons = Math.floor(visibleButtons / 2);

      function createPagination(modal, totalPages) {
        setupPagination(modal, totalPages);
      }

      /*
      * Function to configure pagination
      */
      function setupPagination(modal, totalPages) {
        const wrapper = modal.querySelector('.row');
        const pagination = document.createElement('div');
        pagination.classList.add('pagination-row');

        const nav = document.createElement('nav');
        nav.setAttribute('aria-label', 'Page navigation');
        pagination.appendChild(nav);

        const ul = document.createElement('ul');
        ul.classList.add('pagination');
        nav.appendChild(ul);

        // Botón "Previous"
        const prevLi = document.createElement('li');
        prevLi.classList.add('page-item');
        if (currentPage === 1) prevLi.classList.add('disabled');

        const prevAnchor = document.createElement('a');
        prevAnchor.classList.add('page-link');
        prevAnchor.textContent = 'Previous';
        prevAnchor.href = '#';
        prevLi.appendChild(prevAnchor);
        prevLi.addEventListener('click', () => {
          if (currentPage > 1) {
            currentPage--;
            updatePagination(modal, wrapper, totalPages);
          }
        });
        ul.appendChild(prevLi);

        // Dynamic range of buttons
        let startPage = Math.max(1, currentPage - sideButtons);
        let endPage = Math.min(totalPages, currentPage + sideButtons);

        if (endPage - startPage < visibleButtons - 1) {
          if (currentPage <= sideButtons) {
            endPage = Math.min(totalPages, visibleButtons);
          } else if (currentPage > totalPages - sideButtons) {
            startPage = Math.max(1, totalPages - visibleButtons + 1);
          }
        }

        // Ellipsis and home button (if there are more pages before the first visible button)
        if (startPage > 1) {
          const firstLi = paginationButton(1, modal, wrapper, totalPages);
          ul.appendChild(firstLi);

          const ellipsisLi = document.createElement('li');
          ellipsisLi.classList.add('page-item', 'disabled');
          const ellipsisAnchor = document.createElement('a');
          ellipsisAnchor.classList.add('page-link');
          ellipsisAnchor.textContent = '...';
          ellipsisLi.appendChild(ellipsisAnchor);
          ul.appendChild(ellipsisLi);
        }

        // Dynamic pagination buttons
        for (let i = startPage; i <= endPage; i++) {
          const li = paginationButton(i, modal, wrapper, totalPages);
          ul.appendChild(li);
        }

        // Ellipsis and end button (if there are more pages after the last visible button)
        if (endPage < totalPages - 1) {
          const ellipsisLi = document.createElement('li');
          ellipsisLi.classList.add('page-item', 'disabled');
          const ellipsisAnchor = document.createElement('a');
          ellipsisAnchor.classList.add('page-link');
          ellipsisAnchor.textContent = '...';
          ellipsisLi.appendChild(ellipsisAnchor);
          ul.appendChild(ellipsisLi);
        }

        // Last button (always visible)
        if (endPage < totalPages) {
          const lastLi = paginationButton(totalPages, modal, wrapper, totalPages);
          ul.appendChild(lastLi);
        }

        // "Next" button
        const nextLi = document.createElement('li');
        nextLi.classList.add('page-item');
        if (currentPage === totalPages) nextLi.classList.add('disabled');

        const nextAnchor = document.createElement('a');
        nextAnchor.classList.add('page-link');
        nextAnchor.textContent = 'Next';
        nextAnchor.href = '#';
        nextLi.appendChild(nextAnchor);
        nextLi.addEventListener('click', () => {
          if (currentPage < totalPages) {
            currentPage++;
            updatePagination(modal, wrapper, totalPages);
          }
        });
        ul.appendChild(nextLi);

        wrapper.appendChild(pagination);
      }

      /*
      * Function to create pagination buttons
      */
      function paginationButton(page, modal, wrapper, totalPages) {
        const li = document.createElement('li');
        li.classList.add('page-item');

        const anchor = document.createElement('a');
        anchor.classList.add('page-link');
        anchor.textContent = page;
        anchor.href = '#';
        li.appendChild(anchor);

        if (currentPage === page) li.classList.add('active');

        // Event when a page button is clicked
        li.addEventListener('click', () => {
          currentPage = page;
          updatePagination(modal, wrapper, totalPages, currentPage);
        });

        return li;
      }

      /*
      * Function to update pagination
      */
      function updatePagination(modal, wrapper, totalPages) {
        wrapper.innerHTML = '';
        setupPagination(modal, totalPages);
        scrapingFLV(modal, currentPage);
      }

      /*
      * Function for scraping FLV.
      */
      function scrapingFLV(modal, page) {
        const data = `info=scraping-flv&page=${encodeURIComponent(page)}`;
        createXHR('../includes/download-station.php', data, function (err, responseData) {
          const wrapper = modal.querySelector('.row');
          addAnime(responseData.scraping, wrapper);

          const inputAnime = modal.querySelector('#search-form #searchAnime');
          inputAnime.addEventListener('keyup', function () {
            const inputValue = inputAnime.value.trim();

            wrapper.innerHTML = '';
            if (inputValue.length === 0) {
              addAnime(responseData.scraping, wrapper);
            } else if (inputValue.length > 2) {
              const result = searchAnime(responseData, inputValue);
              addAnime(result, wrapper);
            }
          });

          wrapper.addEventListener('click', function (e) {
            const selected = wrapper.querySelectorAll('.anime.selected');

            if (selected.length == 0) {
              const clickedAnime = e.target.closest('.anime');
              if (clickedAnime && !clickedAnime.classList.contains('active')) {
                clickedAnime.classList.add('active', 'selected');
                const imageContainer = clickedAnime.querySelector('.image');
                if (imageContainer) {
                  addOverlay(imageContainer);
                  handleAnimeClick(clickedAnime, imageContainer);
                }
              }
            }
          });
        });
      }

      /*
      * Function for create span.
      */
      function createSpan(name, spanClass, text, container) {
        setTimeout(() => {
          name = document.createElement('span');

          if (spanClass == 'waiting') {
            name.classList.add(spanClass, 'fade');
          }else{
            name.classList.add(spanClass);
          }

          name.textContent = text;
          container.appendChild(name);

          setTimeout(() => name.classList.add('show'), 50);
        }, 500);
      }

      /*
      * Function for remove span.
      */
      function removeSpan(name, selector, overlay) {
        name = overlay.querySelectorAll('span' + selector);
        name.forEach(span => {
          span.remove();
        });
      }

      /*
      * Function for event click.
      */
      function handleAnimeClick(clickedAnime, imageContainer) {
        const overlay = imageContainer.querySelector('.overlay');
        createSpan('gettingSpan', 'getting-episodes', 'Getting episodes', overlay);
        const url = 'https://www3.animeflv.net' + clickedAnime.getAttribute('data-url');
        scrapingAnime(url, overlay);
      }

      /*
      * Function for adding search to markup.
      */
      function addSearch(modal) {
        const modalBody = modal.querySelector('.modal-content .modal-body');
        const button = modal.querySelector('#initScraping');

        if (!document.getElementById('search-form')) {
          const searchForm = document.createElement('form');
          searchForm.setAttribute('id', 'search-form');
          modalBody.insertBefore(searchForm, button.nextSibling);

          const label = document.createElement('label');
          label.setAttribute('for', 'searchAnime');
          label.classList.add('form-label');
          label.textContent = 'Search';

          const input = document.createElement('input');
          input.setAttribute('type', 'text');
          input.setAttribute('class', 'form-control');
          input.setAttribute('id', 'searchAnime');
          input.setAttribute('placeholder', 'Search Anime');

          searchForm.appendChild(label);
          searchForm.appendChild(input);
        }
      }

      /*
      * Function for search anime.
      */
      function searchAnime(animeArray, term) {
        return animeArray.scraping.filter(anime => anime.title.toLowerCase().includes(term.toLowerCase()));
      }

      /*
      * Function for adding overlay to card.
      */
      function addOverlay(imageContainer) {
        const overlay = document.createElement('div');
        overlay.classList.add('overlay');
        imageContainer.appendChild(overlay);
      }

      /*
      * Function for adding anime to markup.
      */
      function addAnime(animeList, wrapper) {
        animeList.forEach(anime => {
          const colDiv = document.createElement('div');
          colDiv.classList.add('anime-wrapper', 'col-12', 'col-md-4', 'col-lg-3', 'col-xl-2');

          const animeDiv = document.createElement('div');
          animeDiv.classList.add('anime');
          animeDiv.setAttribute('data-url', anime.link);
          colDiv.appendChild(animeDiv);

          const imageDiv = addImage(anime.url, anime.title, anime.type);
          animeDiv.appendChild(imageDiv);
          const titleDiv = addTitle(anime.title);
          animeDiv.appendChild(titleDiv);

          wrapper.appendChild(colDiv);
        });
      }

      /*
      * Function for adding image to markup.
      */
      function addImage(url, title, type) {
        const imageDiv = document.createElement('div');
        imageDiv.classList.add('image');

        const img = document.createElement('img');
        img.setAttribute('src', url);
        img.setAttribute('alt', title + ' logo');
        imageDiv.appendChild(img);

        const typeSpan = document.createElement('span');
        typeSpan.classList.add('type');
        typeSpan.textContent = type;
        imageDiv.appendChild(typeSpan);

        return imageDiv;
      }

      /*
      * Function for adding title to markup.
      */
      function addTitle(title) {
        const titleDiv = document.createElement('div');
        titleDiv.classList.add('text', 'text-center');

        const titleSpan = document.createElement('span');
        titleSpan.textContent = title;
        titleDiv.appendChild(titleSpan);

        return titleDiv;
      }

      /*
      * Function for scraping anime.
      */
      var countEpisodes = 0;
      function scrapingAnime(scrapeUrl, overlay) {
        const url = 'http://localhost:3000/scrape';

        fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ url: scrapeUrl })
        })
        .then(response => response.ok ? response.json() : Promise.reject('Fetch failed'))
        .then(result => {
          const totalEpisodes = result.data.length;

          result.data.forEach(episode => {
            createSpan('episodeSpan', 'episode', episode.episode, overlay);
            scrapingEpisode(episode.link, episode.title, episode.episode, overlay, totalEpisodes);
          });
        })
        .catch(error => console.error('Fetch error:', error));
      }

      /*
      * Function for scraping episodes.
      */
      function scrapingEpisode(url, title, episode, overlay, totalEpisodes) {
        const data = `info=scraping-episode&url=${encodeURIComponent(url)}`;
        createXHR('../includes/download-station.php', data, (err, responseData) => {
          if (!err) {
            varifyEpisode(responseData.episode.link, title, episode, overlay, totalEpisodes);
          }
        });
      }

      /*
      * Function for verify if exist the episo on file station.
      */
      function varifyEpisode(link, title, episode, overlay, totalEpisodes) {
        const data = `info=verify-episode&title=${encodeURIComponent(title)}&episode=${encodeURIComponent(episode)}`;
        createXHR('../includes/download-station.php', data, (err, responseData) => {
          if (!err) {
            removeSpan('gettingEpisodes', '.getting-episodes', overlay);
            removeSpan('episode', '.episode', overlay);

            if (responseData.verify.status === 'true') {
              createSpan('verifySpan', 'verify', episode + ' ' + responseData.verify.error, overlay);

              countEpisodes++;
              if (totalEpisodes == countEpisodes) {
                const selected = document.querySelector('#DownloadStation .anime.selected');

                if (selected) {
                  selected.classList.remove('selected');
                  countEpisodes = 0;
                }
              }
            }
            else{
              scrapingSreamtape(link, title, episode, overlay, totalEpisodes);
            }
          }
        });
      }

      /*
      * Function for scraping download link.
      */
      function scrapingSreamtape(scrapeUrl, title, episode, overlay, totalEpisodes) {
        const url = 'http://localhost:4000/scrape';

        fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ url: scrapeUrl })
        })
        .then(response => response.ok ? response.json() : Promise.reject('Fetch failed'))
        .then(result => {
          if (result.data[0].status === true) {
            createSpan('streamtapeSpan', 'download-link', episode + " Download link obtained", overlay);
            const url = 'https:' + result.data[0].src;
            const encodedUrl = encodeURIComponent(url);
            sendURL(encodedUrl, title, episode, overlay, result.data[0].title, totalEpisodes);
          } else{
            createSpan('streamtapeErrorSpan', 'error-streamtape', episode + ': ' + result.data[0].message, overlay);

            countEpisodes++;
            if (totalEpisodes == countEpisodes) {
              const selected = document.querySelector('#DownloadStation .anime.selected');

              if (selected) {
                selected.classList.remove('selected');
                countEpisodes = 0;
              }
            }
          }
        })
        .catch(error => console.error('Fetch error:', error));
      }

      /*
      * Function for send the download url.
      */
      function sendURL(url, title, episode, overlay, name, totalEpisodes) {
        const data = `info=download-station&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}&episode=${encodeURIComponent(episode)}`;
        createXHR('../includes/download-station.php', data, (err, responseData) => {
          removeSpan('streamtapeSpan', '.download-link', overlay);
          createSpan('downloadSpan', 'download', responseData.download.error, overlay);
          idDownload(name, overlay, episode, title, totalEpisodes);
        });
      }

      /*
      * Function for get the download information.
      */
      let waiting = 0;
      function idDownload(name, overlay, episode, title, totalEpisodes) {
        const intervalDownoad = setInterval(() => {
          const data = `info=id-download&name=${encodeURIComponent(name)}`;
          createXHR('../includes/download-station.php', data, (err, responseData) => {
            if (responseData.id.download === 'waiting') {
              if (waiting === 0) {
                removeSpan('messageSpan', '.download', overlay);
                createSpan('messageSpan', 'waiting', capFirst(responseData.id.download) + '...', overlay);
                waiting++;
              }
            }

            if (responseData.id.download === 'downloading') {
              const id = responseData.id.id;
              clearInterval(intervalDownoad);
              removeSpan('messageSpan', '.waiting', overlay);
              createSpan('downloadingSpan', 'downloading', episode + ' downloading...', overlay);
              infoDownload(id, episode, overlay, title, name, totalEpisodes);
              waiting = 0;
            }
          });
        }, 5000);
      }

      /*
      * Function for capitalize the first letter.
      */
      function capFirst(str) {
        return str[0].toUpperCase() + str.slice(1);
      }

      /*
      * Function for start to download.
      */
      function infoDownload(id, episode, overlay, title, name, totalEpisodes){
        const intervalInfo = setInterval(() => {
          const data = `info=info-download&id=${encodeURIComponent(id)}`;
          createXHR('../includes/download-station.php', data, (err, responseData) => {
            var progress = (responseData.info.download /  responseData.info.size) * 100;
            progress = Math.round(progress);

            if (responseData.info.status === 'error') {
              removeSpan('downloadingSpan', '.downloading', overlay);
              createSpan('messageSpan', 'error',  episode + ': There is a problem with the link, please try again later.', overlay);
              clearInterval(intervalInfo);

              countEpisodes++;
              if (totalEpisodes == countEpisodes) {
                const selected = document.querySelector('#DownloadStation .anime.selected');

                if (selected) {
                  selected.classList.remove('selected');
                  countEpisodes = 0;
                }
              }
            }

            if (responseData.info.status !== 'finished') {
              if (responseData.info.size > 0) {
                const progressDiv = overlay.querySelector(`.progress.${id}`);
                if (!progressDiv) {
                  removeSpan('downloadingSpan', '.downloading', overlay);
                  createSpan('episodeSpan', 'info', episode, overlay);
                  createProgressBar(id, progress, overlay);
                }
                else {
                  const progressBar = overlay.querySelector(`.progress.${id} .progress-bar`);
                  progressBar.style.width = progress + '%';
                  progressBar.textContent = progress + '%';

                  const progressContainer = progressBar.parentElement;
                  progressContainer.setAttribute('aria-valuenow', progress);
                }
              }
            }
            else {
              const progressBar = overlay.querySelector(`.progress.${id} .progress-bar`);
              progressBar.style.width = '100%';
              progressBar.textContent = '100%';

              const progressContainer = progressBar.parentElement;
              progressContainer.setAttribute('aria-valuenow', progress);

              renameFile(title, name, episode, totalEpisodes);
              clearInterval(intervalInfo);
            }
          });
        }, 5000);
      }

      /*
      * Function for add progress bar to overlay.
      */
      function createProgressBar(id, progress, overlay) {
        setTimeout(() => {
          const progressContainer = document.createElement('div');
          progressContainer.classList.add('progress', id);
          progressContainer.setAttribute('role', 'progressbar');
          progressContainer.setAttribute('aria-label', 'Example with label');
          progressContainer.setAttribute('aria-valuenow', '25');
          progressContainer.setAttribute('aria-valuemin', '0');
          progressContainer.setAttribute('aria-valuemax', '100');

          const progressBar = document.createElement('div');
          progressBar.classList.add('progress-bar');
          progressBar.style.width = progress + '%';
          progressBar.textContent = progress + '%';
          progressContainer.appendChild(progressBar);

          overlay.appendChild(progressContainer);
        }, 500);
      }

      /*
      * Function for rename file.
      */
      function renameFile(title, name, episode, totalEpisodes) {
        const data = `info=rename-file&title=${encodeURIComponent(title)}&name=${encodeURIComponent(name)}&episode=${encodeURIComponent(episode)}`;
        createXHR('../includes/download-station.php', data, (err, responseData) => {
          countEpisodes++;
          if (totalEpisodes == countEpisodes) {
            const selected = document.querySelector('#DownloadStation .anime.selected');

            if (selected) {
              selected.classList.remove('selected');
              countEpisodes = 0;
            }
          }
        });
      }
    }
  });
})(jQuery);