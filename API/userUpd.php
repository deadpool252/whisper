<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

    //レスポンス用のデータの枠組みを用意
    $response=[
        "result" => "error", //実行結果を格納(success or error) 成功時にsuccessに書き換える
        "errCode" => null,  //エラーコードがある場合、格納する
        "errMsg" => null,   //エラーメッセージがある場合、格納する
        "list" => [],
    ];
    
    //リクエストの解析
    if($_SERVER['REQUEST_METHOD'] === 'POST'){   //POSTか確認
        /**
	* POSTで送られてきていた場合、JSON形式のデータを取得し、配列に加工。
	* 1. file_get_contents('php://input')：HTTPリクエストのボディ部分からデータを取得(php://inputストリームを使用)
	* 2. json_decode：取得したJSON形式のデータを配列に変換
	* 3. 変数($postData)に変換した配列のデータを格納
	**/
        
        $postData = json_decode(file_get_contents('php://input'),true);
       
    }else{
        //パラメータがない場合エラーメッセージ(ユーザーID:006,パスワード:007)
        echo "error";
        exit();
    }
    require_once './common/errorMsgs.php';//エラーメッセージファイル読み込み
    
    if($postData["userId"] != "" ){
        //内容があれば
        $userId = $postData["userId"];  //取得したメールアドレス代入
    }else{
        //ない場合
       errResult('006');
       exit();
    }
     //パラメータチェック
    if($postData["userName"] == "" && $postData["password"] == ""){
       errResult('002');
       exit();
    }else{
        $userName = $postData["userName"];
        $pass = $postData["password"];
    }
    $profile = $postData["profile"];
    $iconPath = $postData["iconPath"];
    
    require_once "./common/mysqlConnect.php";  //database接続
    
    try{
        $pdo->beginTransaction();
        $image = uniqid(mt_rand(), true); // ファイル名をユニーク化
        // 悪意のあるユーザーに、サーバーに不具合が発生するような名前を設定される恐れがある
        // 長い名前を設定されていると保存できない
        // 保存できないファイル名がある（?.jpgなど)
        $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1); // アップロードされたファイルの拡張子を取得
        $file = "'http://click.ecc.ac.jp/~whisper_c/images/$image";

        
        //ユーザデータを更新するSQL文を実行する
        // 送られてきたユーザIDとパスワードと一致するデータを取得する     
        $sql = "UPDATE user SET userName = :userName, password = :password, profile  = :profile ,iconPath = :iconPath WHERE userId = :userId";
        
        $stmt = $pdo->prepare($sql);   
        $stmt -> bindParam(":userName",$userName,PDO::PARAM_STR);
        $stmt -> bindParam(":password",$pass,PDO::PARAM_STR);
        $stmt -> bindParam(":profile",$profile,PDO::PARAM_STR);
        $stmt -> bindParam(":userId",$userId,PDO::PARAM_STR);
        $stmt -> bindParam(":iconPath",$iconPath,PDO::PARAM_STR);
        if (!empty($_FILES['image']['name'])) {// ファイルが選択されていればimageにファイル名を代入
            move_uploaded_file($_FILES['image']['tmp_name'], 'http://click.ecc.ac.jp/~whisper_c/images/' . $iconPath); //imagesディレクトリにファイル保存
            if (exif_imagetype($file)) {// 画像ファイルかのチェック
                $message = '画像をアップロードしました';
            }
        }
        if ($stmt -> execute() !== false) { // SQL文を実行し、結果がfalseでないかチェックする
            $pdo->commit(); // 成功したらコミット
            $response["result"]= "success";
        } else {
            echo "変更失敗しました。Error: " . $pdo->errorInfo()[2];
            errReuslt('001');
            $pdo->rollBack(); // 失敗したらロールバック
            exit();
        }
        
    } catch (PDOException $e){
        throw new PDOException($e->getMessage(),(int)$e->getCode());
        $pdo->rollBack(); // エラーが発生したらロールバック
    }
        
    $stmt = null;   //SQL情報クローズ
    
    require_once './common/mysqlClose.php';    //データベース接続解除
    
    //レスポンスの送信
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

     
     