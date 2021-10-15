dbUser=process.argv[2];
dbPw=process.argv[3];
db=process.argv[4];
var conn = require('../../db.js');
var boardType=process.argv[5], likeBoardType=process.argv[6], postNo=process.argv[7], like=process.argv[8], memberCode=process.argv[9];
var likeViewQuery = "SELECT `like` FROM `"+boardType+"` WHERE `post_no`= "+postNo;
var likeCheckQuery="SELECT `like` FROM `"+likeBoardType+"` WHERE `post_no`= "+postNo+" AND `member_code`="+memberCode;
var likeQuery="", updateLikeQuery="";
var returnLike=null,postLike=null,likeCheck=null;

if(like>0){
    like=1;
}else if(like<0){
    like=-1;
}else{
    like=0;
}

conn.query(likeCheckQuery, function(error, results, fields){
    if(error){
        console.log(error);
    }else{
        likeCheck=results;
    }
    if(Object.keys(likeCheck).length){//대상 글에 좋아요 또는 싫어요를 누른 적이 있으면
        likeCheck=likeCheck[0].like;
        likeQuery = "UPDATE `"+likeBoardType+"` SET `like`="+like+" WHERE `post_no`="+postNo+" AND `member_code`="+memberCode;
        if(likeCheck==like){//좋아요 또는 싫어요를 한번 더
            likeQuery = "UPDATE `"+likeBoardType+"` SET `like`=0 WHERE `post_no`="+postNo+" AND `member_code`="+memberCode;
            returnLike=0;
            if(like>0){//좋아요를 취소
                updateLikeQuery = "UPDATE `"+boardType+"` SET `like`=`like`-1 WHERE `post_no`="+postNo;
            }else{//싫어요를 취소
                updateLikeQuery = "UPDATE `"+boardType+"` SET `like`=`like`+1 WHERE `post_no`="+postNo;
            }
        }else if(likeCheck==0){//취소한 좋아요 또는 싫어요를 다시 누름
            returnLike=like;
            updateLikeQuery = "UPDATE `"+boardType+"` SET `like`=`like`+"+like+" WHERE `post_no`="+postNo;
        }else{//좋아요에서 싫어요 또는 싫어요에서 좋아요
            if(likeCheck>0){//좋아요에서 싫어요
                returnLike=-1;
                updateLikeQuery = "UPDATE `"+boardType+"` SET `like`=`like`-2 WHERE `post_no`="+postNo;
            }else{//싫어요에서 좋야요
                returnLike=1;
                updateLikeQuery = "UPDATE `"+boardType+"` SET `like`=`like`+2 WHERE `post_no`="+postNo;
            }
        }
    }else{//대상 글에 좋아요 또는 싫어요를 한번도 누른 적이 없으면
        likeQuery = "INSERT INTO `"+likeBoardType+"` (`post_no`, `like`, `member_code`) values ("+postNo+", "+like+", "+memberCode+")";
        updateLikeQuery = "UPDATE `"+boardType+"` SET `like`=`like`+"+like+" WHERE `post_no`="+postNo;
        returnLike=like;
    }
    runLikeQuery();
});
function runLikeQuery(){
    conn.query(likeQuery, function(error, results, fields){
        if(error){
            console.log(error);
        }
        runUpdateLikeQuery();
    });
}
function runUpdateLikeQuery(){
    conn.query(updateLikeQuery, function(error, results, fields){
        if(error){
            console.log(error);
        }
        runLikeViewQuery();
    });
}
function runLikeViewQuery(){
    conn.query(likeViewQuery, function(error, results, fields){
        if(error){
            console.log(error);
        }else{
            postLike=results[0].like;
        }
        sendResult();
    });
}
function sendResult(){
    if(returnLike!=null&&postLike!=null){
        result={
            status:1,
            like:returnLike,
            post_like:postLike,
        };
    }else{
        result={
            status:2,
        };
    }
    console.log(JSON.stringify(result));
    conn.end();
}