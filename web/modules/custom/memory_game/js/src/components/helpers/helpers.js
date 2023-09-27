/**
 * @file
 *   Helpers functions
 */

export const PostFetch = async({url, token, body}) =>
   await fetch(url, {
    method: 'POST',
    mode: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': token,
    },
    body: JSON.stringify(body),
  }).then(res => res.json()).catch(error => alert(error));

export const onGameOver = ({redirect, callback}) => {
  callback();
  setTimeout(()=> {
    window.location.replace(redirect);
  }, 2000)
}

export const redirect = ({path, timeOut= 0}) => {
  setTimeout(()=> window.location.replace(path), timeOut);
}

