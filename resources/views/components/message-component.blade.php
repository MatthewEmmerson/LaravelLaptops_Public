<div id="message-component">
    <!-- Display any success/error messages -->
    <div id="success-error-message-component">
        <x-validation-errors class="mb-4" />
        <x-success-message class="mb-4" />
    </div>

    <!-- Hide the message after five seconds -->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            function removeMessages() {
                let messageComponent = document.getElementById("success-error-message-component");
                messageComponent.remove()
            }

            let timeout = 5000;
            setTimeout(() => { removeMessages() }, timeout);
        });
    </script>
</div>