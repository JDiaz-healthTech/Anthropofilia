// js/tinymce-config.js — Configuración compartida de TinyMCE
// Uso: initTinyMCE({ csrfToken, uploadUrl, contentCss, type })
//   type: 'post' | 'page'

function initTinyMCE(opts) {
    'use strict';
    if (typeof tinymce === 'undefined') return;

    var type = opts.type || 'post';
    var isPage = type === 'page';

    // Plugins: base compartida + postembed solo en páginas
    var plugins =
        'link lists image media table autoresize advlist' +
        ' searchreplace wordcount charmap hr anchor emoticons' +
        ' externalembed' +
        (isPage ? ' postembed' : '');

    // Toolbar: postembed solo en páginas
    var toolbar =
        'undo redo | blocks | bold italic underline strikethrough | ' +
        'alignleft aligncenter alignright alignjustify | ' +
        'bullist numlist outdent indent | ' +
        'link image media table hr | ' +
        'charmap anchor emoticons | ' +
        'externalembed | ' +
        (isPage ? 'postembed | ' : '') +
        'searchreplace';

    var baseContentStyle =
        'body { max-width: 760px; margin: 1rem auto; line-height: 1.7; }' +
        'figure { margin: 1.2rem 0; }' +
        'figcaption { font-size: .9rem; opacity: .8; text-align: center; }' +
        'img { border-radius: 6px; max-width: 100%; height: auto; }' +
        'blockquote { border-left: 4px solid var(--theme-primary, #8a4); padding:.6rem 1rem; background:rgba(0,0,0,.03); }' +
        'table { border-collapse: collapse; width: 100%; }' +
        'table, th, td { border: 1px solid #ddd; }' +
        'th, td { padding: .5rem; }' +
        'ul, ol { margin-left: 1.2rem; }' +
        'pre, code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace; background-color: #f4f4f4; padding: 2px 4px; border-radius: 3px; }' +
        'hr { border: none; border-top: 2px solid #ddd; margin: 2rem 0; }';

    var mediaContentStyle =
        'iframe { max-width: 100%; height: auto; aspect-ratio: 16/9; border-radius: 8px; margin: 1.5rem 0; }' +
        'a img { transition: opacity 0.2s ease, transform 0.2s ease; }' +
        'a:hover img { opacity: 0.85; transform: scale(1.02); }';

    // Configuración base
    var config = {
        selector: '#contenido',
        base_url: 'js/tinymce',
        suffix: '.min',
        plugins: plugins,
        toolbar: toolbar,
        menubar: false,
        height: 540,
        branding: false,

        block_formats:
            'Párrafo=p; Título de sección=h2; Apartado=h3; Subapartado=h4; Cita=blockquote; Preformateado=pre',

        link_target_list: [
            { title: 'Nueva pestaña', value: '_blank' },
            { title: 'Misma pestaña', value: '' },
        ],
        rel_list: [
            { title: 'Ninguno', value: '' },
            { title: 'noopener', value: 'noopener' },
            { title: 'nofollow', value: 'nofollow' },
        ],
        default_link_target: '_blank',

        // Media embeds (YouTube/Vimeo resolver)
        media_live_embeds: true,
        media_alt_source: false,
        media_poster: false,
        media_dimensions: false,
        media_url_resolver: function (data, resolve) {
            // YouTube
            if (data.url.indexOf('youtube.com/watch') !== -1 || data.url.indexOf('youtu.be/') !== -1) {
                var videoId = '';
                if (data.url.indexOf('youtu.be/') !== -1) {
                    videoId = data.url.split('youtu.be/')[1].split(/[?&]/)[0];
                } else {
                    var match = data.url.match(/[?&]v=([^&]+)/);
                    videoId = match ? match[1] : '';
                }
                if (videoId) {
                    resolve({
                        html:
                            '<iframe width="560" height="315" src="https://www.youtube.com/embed/' +
                            videoId +
                            '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
                    });
                    return;
                }
            }
            // Vimeo
            if (data.url.indexOf('vimeo.com/') !== -1) {
                var vimeoId = data.url.split('vimeo.com/')[1].split(/[?&]/)[0];
                if (vimeoId) {
                    resolve({
                        html:
                            '<iframe src="https://player.vimeo.com/video/' +
                            vimeoId +
                            '" width="560" height="315" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>',
                    });
                    return;
                }
            }
            resolve({ html: '' });
        },

        // Imágenes
        images_upload_url: opts.uploadUrl,
        images_upload_credentials: true,
        automatic_uploads: true,
        image_caption: true,
        image_dimensions: false,
        image_class_list: [
            { title: 'Por defecto', value: '' },
            { title: 'Ancho completo', value: 'img-wide' },
        ],

        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', opts.uploadUrl);
                xhr.withCredentials = true;
                xhr.setRequestHeader('X-CSRF-Token', opts.csrfToken);
                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) progress((e.loaded / e.total) * 100);
                };
                xhr.onload = function () {
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP ' + xhr.status);
                        return;
                    }
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location !== 'string') {
                            reject('Respuesta inválida');
                            return;
                        }
                        resolve(json.location);
                    } catch (err) {
                        reject('JSON inválido');
                    }
                };
                xhr.onerror = function () {
                    reject('Error de red');
                };
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('csrf_token', opts.csrfToken);
                xhr.send(formData);
            });
        },

        paste_data_images: false,
        content_css: [opts.contentCss],
        content_style: baseContentStyle + mediaContentStyle,
        browser_spellcheck: true,
        contextmenu: false,
    };

    tinymce.init(config);
}
