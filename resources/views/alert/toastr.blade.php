<style>
    #toast-container>.toast-success,
    #toast-container>.toast-error,
    #toast-container>.toast-info,
    #toast-container>.toast-warning {
        border-radius: 0 !important;
    }
</style>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
</script>

@if (session('success'))
    <script>
        toastr.success("{{ session('success') }}");
    </script>
@endif

@if (session('error'))
    <script>
        toastr.error("{{ session('error') }}");
    </script>
@endif

@if (session('info'))
    <script>
        toastr.info("{{ session('info') }}");
    </script>
@endif

@if (session('warning'))
    <script>
        toastr.warning("{{ session('warning') }}");
    </script>
@endif
