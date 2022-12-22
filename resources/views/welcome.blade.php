<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
    <body>
        <h1>ファイルアップロードテスト</h1>
        <div id="droppable" style="border: gray solid 1em; padding: 2em;">
        ファイルをドロップしてください。
        </div>
        <button id="hugefile" type="submit" class="btn btn-primary">送信</button>

        <br>
        <br>
        <input type="file" name="upfile" id="upfile" accept="image/*">
	    <button type="button" name="button" id="button">送信する</button>

        <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
        <script>
            let upload_file;
            let filename = @json($filename);
            let pre_signed_url = @json($pre_signed_url)
            
            $(function() {
                let droppable = $("#droppable");

                console.log(filename)
                console.log(pre_signed_url)

                // File API が使用できない場合は諦めます.
                if(!window.FileReader) {
                alert("File API がサポートされていません。");
                return false;
                }

                let cancelEvent = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

                droppable.bind("dragenter", cancelEvent);
                droppable.bind("dragover", cancelEvent);

                // ドロップ時のイベントハンドラを設定します.
                let handleDroppedFile = function(event) {
                // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
                let file = event.originalEvent.dataTransfer.files[0];
                $("#droppable").text("[" + file.name + "]");
                upload_file = file
                console.log(upload_file)
                cancelEvent(event);
                return false;
                }

                droppable.bind("drop", handleDroppedFile);
                
            });

            //////////////////////////////////////////////////////
            ////    ここから先がS3アップロードに使用されるコード部分   ////    
            //////////////////////////////////////////////////////

            // ボタンが押されたときの処理
            $("#hugefile").on('click', function () {
                if (upload_file == null) {
                    alert('ファイルがない')
                    return false
                }

                // $.ajax({
                //     url: "/one-time.php",
                //     data: {"name":upload_file.name},
                //     type: 'GET',
                //     dataType: 'json'
                // }).done(data => sendFileCore(data, upload_file))

                // return false
                $.ajax({
                    url: pre_signed_url,
                    type: 'PUT',
                    data: upload_file,
                    // contentType: file.type,
                    processData: false
                }).done(function(d) {
                    alert('完了')
                })
            })

            // Ajaxで取得した署名付きURLを使用してファイルをアップロードする処理
            // function sendFileCore(data, file) {
            //     $.ajax({
            //         url: data.uri,
            //         type: 'PUT',
            //         data: file,
            //         contentType: file.type,
            //         processData: false
            //     }).done(function(d) {
            //         alert('完了')
            //     })
            // }
        </script>
        <script>
            $(function(){
                var file;
                let upload_file;
                let filename = @json($filename);
                let pre_signed_url = @json($pre_signed_url)

                $("#upfile").change(function(){
                    console.log("upload file");
                    if (this.files.length > 0) {
                        file = this.files[0];
                    }
                });

                $("#button").click(function(){
                    console.log(file);
                    var filetype = file["type"];
                    // var imgurl = "アップロード先のURL";
                    $.ajax({
                        type: "PUT",
                        url: pre_signed_url,
                        data: file,
                        headers:{
                            "Content-Type":filetype
                        },
                        processData: false,
                        contentType: false
                    })
                    .done(function(json_data) {
                        console.log("success");
                    })
                    .fail(function() {
                        alert("error");
                    });
                });
            });
        </script>
    </body>
</html>