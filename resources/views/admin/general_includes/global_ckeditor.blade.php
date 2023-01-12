<script src="{{ asset('/assets/vendors/js/editors/ckeditor/ckeditor.js') }}" type="text/javascript"></script> 
<script type="text/javascript">
$(".ckeditor").each(function(){
var editor_id = $(this).attr("id");
    CKEDITOR.replace( editor_id, {
    toolbar: [
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
    '/',
    { name: 'styles', items: [ 'Styles', 'Format' ] },
    { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
    { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
    { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source'] },
]
});
});
</script>