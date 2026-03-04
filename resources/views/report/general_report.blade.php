<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        body {font-family: Arial, helvetica, sans-serif; font-size: 14px;}
        table {border-collapse: collapse; width:100%;}
        th, td {border: 1px solid #ddd; padding: 8px;}
        th {background-color: #f2f2f2; text-align: left;}
        .header {text-align: center; margin-bottom: 20px;}

    </style>
</head>
<body>
    <div class="header">
        <h2>General Report</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($leaves as $leave)
                <tr>
                    <td>{{ $leave->user->fname }} {{ $leave->user->lname }}</td>
                    <td>{{ $leave->user->department->name ?? '-' }}</td>
                    <td>{{ ucfirst($leave->request_type) }}</td>
                    <td>{{ $leave->start_date }}</td>
                    <td>{{ $leave->end_date }}</td>
                    <td>{{ ucfirst($leave->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
