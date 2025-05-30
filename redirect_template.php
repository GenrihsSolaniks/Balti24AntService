<?php
// redirect_template.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $template = $_POST["template"] ?? '';

    switch ($template) {
        case "1":
            header("Location: worker_akt_1.php");
            break;
        case "2":
            header("Location: worker_akt_2.php");
            break;
        case "3":
            header("Location: worker_akt_3.php");
            break;
        default:
            header("Location: choose_template.php");
            break;
    }
    exit;
}
?>
