/*
 * @param {String} searchKey 検索するキー
 * @returns {String} キーに対応する値
 */
export function getCookieValue(searchKey) { //クッキーの値を取得する
  if(typeof searchKey === 'undefined') {
    return '';
  }
  let val = '';

  document.cookie.split(';').forEach(cookie => { // document.cookie によってクッキーは以下の形式で参照できる
    const [key, value] = cookie.split('=');      //「name=12345;token=67890;key=abc」
    if(key === searchKey) {
      return val = value;
    }
  })
  return val;
}

/**
 * ステータスコードの定義
 * 他のプログラムはこれをインポートして使うようにする
 */
export const OK                    = 200; //OK
export const CREATED               = 201; //リソースの作成
export const NOT_FOUND             = 404; //404エラー
export const CONFLICT              = 409; //パスワード競合
export const UNAUTHORIZED          = 419; //認証切れ
export const UNPROCESSABLE_ENTITY  = 422; //laravelのバリデーションエラーは422
export const TOO_MANY_REQUEST      = 429; //指定回数のリクエストを超えたとき
export const INTERNAL_SERVER_ERROR = 500; //500エラー
