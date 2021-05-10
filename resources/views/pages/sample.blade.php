<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        #container_new_table thead {
			position: -webkit-sticky;
			position: sticky;
			top: 0;
			z-index: 5;
			background: #fff;
		}
    </style>
</head>
<body>
        <div style="height: 500px;overflow-y:scroll">
                <table id="container_new_table" class="">
                    <thead>
                        <tr>
                            <th>bl</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $d)
                            <tr>
                            <td>{{$d['bl_no']}}</td>
                            </tr>
                        @endforeach
                      
                    </tbody>
                </table>
            </div>
</body>
</html>

