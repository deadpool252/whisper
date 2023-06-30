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
        "userList" => [],
        "whisperList" =>[],
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
    
    
    //パラメータチェック
    if($postData["section"] != ""){
        //内容があれば
        $section = $postData["section"];  //取得したメールアドレス代入
    }else{
        //ない場合
       errResult('009');
       exit();
    }
    
    if($postData["string"]!= ""){
        //内容があれば
        $string = $postData["string"];  //取得したパスワード代入
    }else{
        //ない場合
       errResult('010');
       exit();
    }
    
    if(!($postData["section"]==1||$postData["section"]==2)){
        //ない場合
       errResult('016');
       exit();
    }
    
     require_once "./common/mysqlConnect.php"; //database接続
        
    
    try{
        if($section == 1){
            // 送られてきたユーザIDとパスワードと一致するデータを取得する   
            $sql = "select u.userId, u.userName, w.cnt as whisperCnt, fl.cnt as followCnt, fr.cnt as followerCnt ";
            $sql .= "from user u left join whisperCntView w on w.userId=u.userId left join followCntView fl on fl.userId=w.userId left join followerCntView fr on fr.followUserId=w.userId ";
            $sql .= "WHERE u.userId = :userId or u.userName = :userName";

            $stmt = $pdo->prepare($sql);   
            $stmt -> bindParam(":userId",$string,PDO::PARAM_STR);
            $stmt -> bindParam(":userName",$string,PDO::PARAM_STR);
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
                array_push($response["userList"],[
                    "userId" => $row["userId"],
                    "userName" => $row["userName"],
                    "whisperCount" => $whisperCnt,
                    "followCount" => $followCnt,
                    "followerCount" => $followerCnt
                ]);
            }
        }else{
            // 送られてきたユーザIDとパスワードと一致するデータを取得する
            $sql = "select w.whisperNo, w.userId, u.userName, w.postDate, w.content, g.whisperno, g.cnt ";
            $sql .= "from whisper w left join user u on u.userId=w.userId left join goodCntView g on g.whisperNo=w.whisperNo ";
            $sql .= "where w.content like '%".$string."%'";

            $stmt = $pdo->prepare($sql);
            $stmt -> execute();
            
            while($row = $stmt->fetch()){
                if($row["cnt"]==null){
                    $num = 0;
                }else{
                    $num = $row["cnt"];
                }
                array_push($response["whisperList"],[
                    "whisperNo" => $row["whisperNo"],
                    "userId" => $row["userId"],
                    "userName" => $row["userName"],
                    "postDate" => $row["postDate"],
                    "content" => $row["content"],
                    "goodCount" => $num
                ]);
            }
        }    
        $response["result"] = "success";
    } catch (PDOException $e) {
           throw new PDOException($e->getMessage(),(int)$e->getCode());
    }    

    $stmt = null;   //SQL情報クローズ

    require_once './common/mysqlClose.php';    //データベース接続解除
    
    
    //レスポンスの送信
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

    
    
    
   
    