<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>ファイルアップロードテスト</h1>
        {{-- ここからがファイルドラックアンドドロップ式の部分 --}}
        <div id="droppable" style="border: gray solid 1em; padding: 2em;">
        ファイルをドロップしてください。
        </div>
        <button id="hugefile" type="submit" class="btn btn-primary">送信</button>
        <br>
        <br>
        {{-- ここからがファイル選択式の部分 --}}
        <input type="file" name="upfile" id="upfile" accept="image/*">
	    <button type="button" name="button" id="button">送信する</button>

        <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
        <script>
            let upload_file; //グローバル変数
            $(function() {
                var droppable = $("#droppable");

                // File API が使用できない場合は諦めます.
                if(!window.FileReader) {
                    alert("File API がサポートされていません。");
                    return false;
                }

                // イベントをキャンセルするハンドラ
                var cancelEvent = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

                // dragenter, dragover イベントのデフォルト処理をキャンセルします
                droppable.bind("dragenter", cancelEvent);
                droppable.bind("dragover", cancelEvent);

                // ドロップ時のイベントハンドラを設定します.
                var handleDroppedFile = function(event) {
                    // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
                    var file = event.originalEvent.dataTransfer.files[0];
                    // event.target.result に読み込んだファイルの内容が入っています.
                    // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax で送信
                    $("#droppable").text("[" + file.name + "]");
                    upload_file = file

                    // デフォルトの処理をキャンセルします.
                    cancelEvent(event);
                    return false;
                }
                // ドロップ時のイベントハンドラを設定します.
                droppable.bind("drop", handleDroppedFile);
            });

            //////////////////////////////////////////////////////
            ////    ここから先がS3アップロードに使用されるコード部分   ////    
            //////////////////////////////////////////////////////

            // ボタンが押されたときの処理
            $("#hugefile").on('click', function () {
                // ファイル選択がない場合
                if (upload_file == null) {
                    alert('ファイルがない')
                    return false
                }
                // GETリクエストで署名済みURLと一意のファイル名を取得する
                $.ajax({
                    url: "{{route('s3.getPresignedUrl')}}",
                    data: {"filename":upload_file.name},
                    type: 'GET',
                    dataType: 'json'
                }).done(data => sendFileCore(data, upload_file)) // アップロード
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
                    $("#droppable").text("");
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
                let upload_file; //グローバル変数

                // ファイルを選択すると発火しfileを読み込む
                $("#upfile").change(function(){
                    console.log("upload file");
                    if (this.files.length > 0) {
                        upload_file = this.files[0];
                    }
                });
                // ボタンが押されたときの処理
                $("#button").click(function(){
                    var filetype = upload_file["type"];
                    // GETリクエストで署名済みURLと一意のファイル名を取得する
                    $.ajax({
                        url: "{{route('s3.getPresignedUrl')}}",
                        data: {"filename":upload_file.name},
                        type: 'GET',
                        dataType: 'json'
                    }).done(data => sendFileCore(data, upload_file)) // アップロード
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