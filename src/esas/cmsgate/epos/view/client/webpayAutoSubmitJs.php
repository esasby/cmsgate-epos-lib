<?php
/** @var array $scriptData */
/** @var \esas\cmsgate\epos\view\client\CompletionPanelEpos $completionPanel */
$completionPanel = $this->scriptData["completionPanel"];
?>

<script type="text/javascript">
    var webpay_form = $('#webpay form');
    webpay_form.submit();
</script>