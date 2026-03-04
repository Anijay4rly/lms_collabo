<div class="col-lg-3 col-md-6 col-12">
    <div class="small-box bg-info">
        <div class="inner">
            <h3>{{ $submittedCount ?? 0 }}</h3>
            <p>My Requests</p>
        </div>
        <div class="icon">
            <i class="fas fa-list"></i>
        </div>
        <a href="{{ route('leaves.showmy') }}" class="small-box-footer">
            View Info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-lg-3 col-md-6 col-12">
    <div class="small-box bg-warning">
        <div class="inner">
            <h3>{{ $pendingCount ?? 0 }}</h3>
            <p>Pending</p>
        </div>
        <div class="icon">
            <i class="fas fa-clock"></i>
        </div>
        <a href="{{ route('leaves.pending') }}" class="small-box-footer">
            View Info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-lg-3 col-md-6 col-12">
    <div class="small-box bg-success">
        <div class="inner">
            <h3>{{ $approvedCount ?? 0 }}</h3>
            <p>Approved</p>
        </div>
        <div class="icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <a href="{{ route('leaves.approved') }}" class="small-box-footer">
            View Info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-lg-3 col-md-6 col-12">
    <div class="small-box bg-danger">
        <div class="inner">
            <h3>{{ $rejectedCount ?? 0 }}</h3>
            <p>Rejected</p>
        </div>
        <div class="icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <a href="{{ route('leaves.rejected') }}" class="small-box-footer">
            View Info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>
