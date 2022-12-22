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
            $(function() {
                var droppable = $("#droppable");

                // File API が使用できない場合は諦めます.
                if(!window.FileReader) {
                    alert("File API がサポートされていません。");
                    return false;
                }

                var cancelEvent = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

                droppable.bind("dragenter", cancelEvent);
                droppable.bind("dragover", cancelEvent);

                // ドロップ時のイベントハンドラを設定します.
                var handleDroppedFile = function(event) {
                    // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
                    var file = event.originalEvent.dataTransfer.files[0];
                    $("#droppable").text("[" + file.name + "]");
                    upload_file = file
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

                $.ajax({
                    url: "{{route('s3.getPresignedUrl')}}",
                    data: {"filename":upload_file.name},
                    type: 'GET',
                    dataType: 'json'
                }).done(data => sendFileCore(data, upload_file))
                return false
            })

            // Ajaxで取得した署名付きURLを使用してファイルをアップロードする処理
            function sendFileCore(data, file) {
                // console.log(data)
                $.ajax({
                    url: data.pre_signed_url,
                    type: 'PUT',
                    data: file,
                    contentType: file.type,
                    processData: false
                })
                .done(function(json_data) {
                    alert("S3保存完了");
                })
                .fail(function() {
                    alert("error")
                })
            }
        </script>

        {{-- インプットでファイル選択タイプのスクリプト --}}
        <script>
            $(function(){
                let upload_file;

                $("#upfile").change(function(){
                    console.log("upload file");
                    if (this.files.length > 0) {
                        upload_file = this.files[0];
                    }
                });

                $("#button").click(function(){
                    var filetype = upload_file["type"];

                    $.ajax({
                        url: "{{route('s3.getPresignedUrl')}}",
                        data: {"filename":upload_file.name},
                        type: 'GET',
                        dataType: 'json'
                    }).done(data => sendFileCore(data, upload_file))

                    // $.ajax({
                    //     type: "PUT",
                    //     url: pre_signed_url,
                    //     data: file,
                    //     headers:{
                    //         "Content-Type":filetype
                    //     },
                    //     processData: false,
                    //     contentType: false
                    // })
                    // .done(function(json_data) {
                    //     console.log("success");
                    // })
                    // .fail(function() {
                    //     alert("error");
                    // });
                });
            });

            // Ajaxで取得した署名付きURLを使用してファイルをアップロードする処理
            function sendFileCore(data, file) {
                console.log(data)
                $.ajax({
                    url: data.pre_signed_url,
                    type: 'PUT',
                    data: file,
                    contentType: file.type,
                    processData: false
                })
                .done(function(json_data) {
                    alert("S3保存完了");
                })
                .fail(function() {
                    alert("error")
                })
            }
        </script>
    </body>
</html>