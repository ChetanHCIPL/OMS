@extends('layouts.admin')
@section('styles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<div class="content-wrapper">
    <div class="content-body">
        <section class="horizontal-grid" id="horizontal-grid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="material-icons">warning</i> Unauthorised Access</h4>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                               <div class="alert alert-danger">
									<h4>Unauthorised Access. Contact Administrator..!!</h4>
							   </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
