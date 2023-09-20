<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container jumbotron">
        <div class="text-center">
            <h1>Upload Image With GridFS</h1>
            @error('file')
                {{$message}}
            @enderror
            {{-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
        </div>
        <div class="row d-flex justify-content-center py-2">
            <div class="col-md-6">
                <form action="/upload/" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" id="customFile">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                    <input type="submit" value="Submit" class="btn btn-primary btn-block my-2">
                </form>
                @foreach ($files as $file)
                <div class="card mb-2">
                    <div class="card-header">{{$file->metadata->file_name}} - ({{$file->metadata->mimeType}})</div>
                    <div class="card-body">
                        @if (in_array($file->metadata->mimeType, ['image/png', 'image/jpg', 'image/jpeg', 'image/gif']))
                        <img src="http://127.0.0.1:8000/get-file/{{$file->_id}}" width="100%" height="200"/>
                        @elseif (in_array($file->metadata->mimeType, ['video/mp4']))
                        <video src="http://127.0.0.1:8000/get-file/{{$file->_id}}" width="100%" height="200" controls></video>
                        @else
                        <a href="http://127.0.0.1:8000/get-file/{{$file->_id}}">{{$file->metadata->file_name}} (Click here to download)</a>
                        @endif
                        <a href="http://127.0.0.1:8000/delete-file/{{$file->_id}}" class="btn btn-danger btn-block mt-2">Delete</a>
                    </div>
                    <div class="card-footer">File ID - {{$file->_id}}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
</body>
</html>
