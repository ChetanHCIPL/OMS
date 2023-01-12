<button type="button" class="btn btn-outline-success block btn-lg" data-toggle="modal" id="msg-modal" style="display: none;" data-target="#msg-modal-popup">Launch Modal</button>
<!-- Modal -->
<div class="modal fade text-left" id="msg-modal-popup" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content redirectTab" id="msg-html">
		</div>
	</div>
</div>
<div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

<!-- BEGIN: Loading-->
<div class="loader-wrapper data_loader" style="display:none">
  <div class="loader-container">
    <div class="ball-spin-fade-loader loader-blue">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <p>Loading...</p>
    </div>
  </div>
</div>
<!-- END: Loading-->
<script type="text/javascript">
  currentYear = "{{date('Y');}}";
</script>
<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light navbar-shadow">
    <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2"><span class="float-md-left d-block d-md-inline-block">Copyright &copy; {{date('Y');}} <span id="scroll-top"></span></span></p>
</footer>
<!-- END: Footer-->