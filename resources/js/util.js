//ステータスコードの定義
//他のプログラムはこれをインポートして使うようにする
export const OK                    = 200; //OK
export const CREATED               = 201;
export const UNAUTHORIZED          = 419; //認証切れ
export const UNPROCESSABLE_ENTITY  = 422; //laravelのバリデーションエラーは422
export const NOT_FOUND             = 404; //404エラー
export const TOO_MANY_REQUEST      = 429; //指定回数のリクエストを超えたとき
export const INTERNAL_SERVER_ERROR = 500; //500エラー
