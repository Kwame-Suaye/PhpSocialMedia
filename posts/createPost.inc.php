<?php
    if(isset($_POST['postContent'])){

        //Opens a connection to the database and starts the session
        require '../includes/dbconnect.inc.php';

        $conn = OpenCon();
        session_start();

        //gets the data to be inserted into the database
        $postHead = $_POST['postHead'];
        $postContent = $_POST['postContent'];

        $userId = $_SESSION['userId'];
        $username = $_SESSION['username'];
        $timestamp = time();
        $likes = 0;
        $edit_timestamp = 0;

        //Error handlers for empty post_head and post_content
        if($postHead == ""){
            $error = "emptyHead";
            $postId = '';
        }
        else if($postContent == ""){
            $error = "emptyContent";
            $postId = '';
        }
        else{
            //inserts the data into the database
            $sql = "INSERT INTO user_posts (user_id, username, post_timestamp, likes, head, content, edit_timestamp) VALUES (?,?,?,?,?,?,?)";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                $error = 'sqlError';
                $postId ='';
            }
            else{
                mysqli_stmt_bind_param($stmt, "isiissi", $userId, $username, $timestamp, $likes, $postHead, $postContent, $edit_timestamp);
                mysqli_stmt_execute($stmt);
                
                

                $sql = "SELECT * FROM user_posts WHERE user_id=$userId AND post_timestamp=$timestamp";
                if($result = mysqli_query($conn, $sql)){
                    $row = mysqli_fetch_row($result);
                    $postId = $row[0];

                    $error = 'noError';
                    require 'userPosts.php';
                }
                else{
                    $error = 'sqlError';
                    $postId = '';
                }
            }
        }


    }
    //If the user just typed in the url to this file, they'll be redirected to the initial page
    else{
        header('location: ../index.php');
        exit();  
    }


?>

<script>
    //Gets the error code from the above php script
    var errorCode = '<?php echo $error; ?>';

    //Resets the border of the inputs
    $('.post-creator-head, post-creator-content').css('border', '2px solid #3d3d3d');

    if(errorCode == "sqlError"){
        alert("SQL Error");
    }
    else if(errorCode == "emptyHead"){
        $('.post-creator-head').css('border', '2px solid red');
    }
    else if(errorCode == "emptyContent"){
        $('.post-creator-content').css('border', '2px solid red');
    }
    //If there were no errors
    else{
        //Resets the input fields
        $('.post-creator-head, .post-creator-content').css('border', '2px solid #3d3d3d');
        $('.post-creator-head, .post-creator-content').val('');

        //Shows the post
        $(".profile-post-creator-wrapper").after(`<?php echo getSinglePost($postId, $_SESSION["username"], date('d/m/Y',$timestamp), $likes, htmlspecialchars(addslashes($postHead)), htmlspecialchars($postContent), date('d/m/Y', $timestamp)); ?>`)
        $('#post<?php echo $postId ?>').hide().delay(200).fadeIn(500);
    }
    
</script>