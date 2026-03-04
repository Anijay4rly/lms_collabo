<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Taarifa ya Kuwa Nje ya Kituo</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 40px;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .section-title {
            font-weight: bold;
            margin: 30px 0 15px;
            text-decoration: underline;
            font-size: 15px;
        }
        .signature-img {
            max-height: 60px;
            vertical-align: middle;
        }
        .mt-3 { margin-top: 20px; }
        .field-value {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>

@php
    $user = $leaveRequest->user;
    $department = $user?->department;

    $fullName = $user ? trim($user->fname.' '.$user->mname.' '.$user->lname) : null;

    $days = ($leaveRequest->start_date && $leaveRequest->end_date)
        ? \Carbon\Carbon::parse($leaveRequest->start_date)
            ->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1
        : null;

    $startDate = $leaveRequest->start_date
        ? \Carbon\Carbon::parse($leaveRequest->start_date)->format('d/m/Y')
        : null;

    $endDate = $leaveRequest->end_date
        ? \Carbon\Carbon::parse($leaveRequest->end_date)->format('d/m/Y')
        : null;

    $createdDate = $leaveRequest->created_at
        ? $leaveRequest->created_at->format('d/m/Y H:i')
        : null;

    // HOD and Admin histories
    $hodHistory = $leaveRequest->leavehistories
        ->where('user.role', 'hod')
        ->sortByDesc('created_at')
        ->first();

    $adminHistory = $leaveRequest->leavehistories
        ->where('user.role', 'admin')
        ->sortByDesc('created_at')
        ->first();

    function getBase64Signature($path) {
        if(!$path) return null;
        $cleanPath = str_replace('storage/', '', $path);
        $fullPath = storage_path('app/public/' . $cleanPath);
        if(file_exists($fullPath)){
            $type = pathinfo($fullPath, PATHINFO_EXTENSION);
            $data = file_get_contents($fullPath);
            return 'data:image/'.$type.';base64,'.base64_encode($data);
        }
        return null;
    }

    $hodBase64 = getBase64Signature($hodHistory?->user?->signature);
    $adminBase64 = getBase64Signature($adminHistory?->user?->signature);

    // Labels for remarks
    $hodLabel = ($leaveRequest->status === 'pending') ? 'Naidhinisha' : 'Siidhinishi';
    $adminLabel = ($leaveRequest->status === 'approved') ? 'Naidhinisha' : 'Siidhinishi';
@endphp

<h3 class="text-center bold">WIZARA YA AFYA</h3>
<h4 class="text-center bold" style="text-decoration: underline;">
    TAARIFA YA KUWA NJE YA KITUO
</h4>

{{-- SEHEMU A --}}
<div class="section-title">SEHEMU A: (TAARIFA YA RUHUSA)</div>
<div class="mt-3">
    1. (a) Mimi <span class="field-value">{{ $fullName ?? '......................' }}</span>
    wa Idara/Kitengo cha <span class="field-value">{{ $department?->name ?? '......................' }}</span>
    naomba ruhusa ya kuwa nje ya kituo cha kazi kwa siku
    <span class="field-value">{{ $days ?? '..........' }}</span>
    kuanzia tarehe <span class="field-value">{{ $startDate ?? '......................' }}</span>
    hadi tarehe <span class="field-value">{{ $endDate ?? '......................' }}</span>.
</div>

<div class="mt-3">
    (b) Sababu ya kuwa nje ya kituo cha kazi:
    <span class="field-value">
        {{ $leaveRequest->reasons ?? '.............................................................................' }}
    </span>
</div>

<div class="mt-3">
    Sahihi <span class="field-value">........................</span>
    Cheo <span class="field-value">{{ $user->assigned_as ?? '.....................' }}</span>
    Tarehe <span class="field-value">{{ $createdDate ?? '........................' }}</span>
</div>

{{-- SEHEMU B: HOD --}}
<div class="section-title">SEHEMU B: (IMEIDHINISHWA NA MKUU WA IDARA/KITENGO)</div>
<div class="mt-3">
    2.(a) {{ $hodLabel }} ombi kwa sababu zifuatazo:
    <span class="field-value">{{ $hodHistory?->remarks ?? '.............................................................................' }}</span>
</div>
<div class="mt-3">
    Sahihi
    @if($hodBase64)
        <img src="{{ $hodBase64 }}" class="signature-img">
    @else
        <span class="field-value">........................</span>
    @endif
    Cheo <span class="field-value">{{ $hodHistory?->user?->assigned_as ?? '.....................' }}</span>
    Tarehe <span class="field-value">{{ $hodHistory?->created_at ? $hodHistory->created_at->format('d/m/Y H:i') : '........................' }}</span>
</div>

{{-- SEHEMU C: ADMIN --}}
<div class="section-title">SEHEMU C: (IDHINI YA AFISA MWAJIRI)</div>
<div class="mt-3">
    {{ $adminLabel }} ombi kwa sababu zifuatazo:
    <span class="field-value">{{ $adminHistory?->remarks ?? '.............................................................................' }}</span>
</div>
<div class="mt-3">
    Sahihi
    @if($adminBase64)
        <img src="{{ $adminBase64 }}" class="signature-img">
    @else
        <span class="field-value">........................</span>
    @endif
    Cheo <span class="field-value">{{ $adminHistory?->user?->assigned_as ?? '.....................' }}</span>
    Tarehe <span class="field-value">{{ $adminHistory?->created_at ? $adminHistory->created_at->format('d/m/Y H:i') : '........................' }}</span>
</div>

</body>
</html>
