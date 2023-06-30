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
        "followList" => [],
        "followerList" => []
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
        //パラメータがない場合エラーメッセージ
        echo "error";
        exit();
    }
    require_once './common/errorMsgs.php';//エラーメッセージファイル読み込み
    
    // パラメータの必須チェックを行う。
    if($postData["userId"] != "" ){
        //内容があれば
        $userId = $postData["userId"];  //取得したメールアドレス代入
    }else{
       errResult('006');
       exit();
    }
    
    require_once "./common/mysqlConnect.php";
    
    try{
        $sql = "select f.followUserId, u.userName, w.cnt as whisperCnt, fv.cnt as followCnt, ferv.cnt as followerCnt ";
        $sql .= "from follow f ";
        $sql .= "left join user u on u.userId = f.followUserId ";
        $sql .= "left join whisperCntView w on w.userId = f.followUserId ";
        $sql .= "left join followCntView fv on fv.userId = f.followUserId ";
        $sql .= "left join followerCntView ferv on ferv.followUserId = f.followUserId ";
        $sql .= "where f.userId = :userId";
        
        $stmt = $pdo->prepare($sql);   
        $stmt -> bindParam(":userId",$userId,PDO::PARAM_STR);
        $stmt -> execute();
        
        while($row = $stmt->fetch()){
            if($row["whisperCnt"]==null){
                $whisperCnt = 0;
            }else{
                $whisperCnt = $row["whisperCnt"];
            }
            if($row["followCnt"]==null){
                $followCnt = 0;
            }else{
                $followCnt = $row["followCnt"];
            }
            if($row["followerCnt"]==null){
                $followerCnt = 0;
            }else{
                $followerCnt = $row["followerCnt"];
            }
            array_push($response["followList"],[
                "userId" => $row["followUserId"],
                "userName" => $row["userName"],
                "whisperCount" => $whisperCnt,
                "followCount" => $followCnt,
                "followerCount" => $followerCnt
            ]);
        }
        $stmt = null;   // 5.SQL情報クローズ
        
        $sql2 = "select f.userId, u.userName, w.cnt as whisperCnt, fv.cnt as followCnt, ferv.cnt as followerCnt ";
        $sql2 .= "from follow f ";
        $sql2 .= "left join user u on u.userId = f.userId ";
        $sql2 .= "left join whisperCntView w on w.userId = f.userId ";
        $sql2 .= "left join followCntView fv on fv.userId = f.userId ";
        $sql2 .= "left join followerCntView ferv on ferv.followUserId = f.userId ";
        $sql2 .= "where f.followUserId = :userId";
        
        
        $stmt2 = $pdo->prepare($sql2);   
        $stmt2 -> bindParam(":userId",$userId,PDO::PARAM_STR);
        $stmt2 -> execute();
        
        while($row = $stmt2->fetch()){
            if($row["whisperCnt"]==null){
                $whisperCnt = 0;
            }else{
                $whisperCnt = $row["whisperCnt"];
            }
            if($row["followCnt"]==null){
                $followCnt = 0;
            }else{
                $followCnt = $row["followCnt"];
            }
            if($row["followerCnt"]==null){
                $followerCnt = 0;
            }else{
                $followerCnt = $row["followerCnt"];
            }
            array_push($response["followerList"],[
                "userId" => $row["userId"],
                "userName" => $row["userName"],
                "whisperCount" => $whisperCnt,
                "followCount" => $followCnt,
                "followerCount" => $followerCnt
            ]);
        }
        $stmt2 = null;   // 5.SQL情報クローズ
        $response["result"]= "success";
    }catch (PDOException $e) {
           throw new PDOException($e->getMessage(),(int)$e->getCode());
    }    
    
    require_once './common/mysqlClose.php';    //データベース接続解除
     //レスポンスの送信
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
    
    