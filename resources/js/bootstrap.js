import { getCookieValue } from "./util";

window.axios = require('axios');

//Ajaxリクエストであることを示すヘッダーを付与する
window.axios.defaults.headers.common['X-Requested-Wi' +
'th'] = 'XMLHttpRequest';

window.axios.interceptors.request.use(config => {
  //クッキーからトークンを取り出してヘッダーに付与する
  config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN');

  return config;
});

//axiosのresponse
window.axios.interceptors.response.use(
  response => response, //成功時の処理
  error => error.response || error //失敗時の処理
);


// window._ = require('lodash');
//
// /**
//  * We'll load jQuery and the Bootstrap jQuery plugin which provides support
//  * for JavaScript based Bootstrap features such as modals and tabs. This
//  * code may be modified to fit the specific needs of your application.
//  */
//
// try {
//     window.Popper = require('popper.js').default;
//     window.$ = window.jQuery = require('jquery');
//
//     require('bootstrap');
// } catch (e) {}
//
// /**
//  * We'll load the axios HTTP library which allows us to easily issue requests
//  * to our Laravel back-end. This library automatically handles sending the
//  * CSRF token as a header based on the value of the "XSRF" token cookie.
//  */
//
// window.axios = require('axios');
//
// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
//
// /**
//  * Echo exposes an expressive API for subscribing to channels and listening
//  * for events that are broadcast by Laravel. Echo and event broadcasting
//  * allows your team to easily build robust real-time web applications.
//  */
//
// // import Echo from 'laravel-echo';
//
// // window.Pusher = require('pusher-js');
//
// // window.Echo = new Echo({
// //     broadcaster: 'pusher',
// //     key: process.env.MIX_PUSHER_APP_KEY,
// //     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
// //     encrypted: true
// // });
