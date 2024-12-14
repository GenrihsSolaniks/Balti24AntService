<?php
if (!isset($_COOKIE['user'])) {
    header('Location: index.php'); // Перенаправление на index.php
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balti24.com</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleform.css">
</head>
<body>
    <h1>Welcome, <?=htmlspecialchars($_COOKIE['user'])?>!</h1>
    <p><a href="exit.php">Log out</a></p>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Welcome to Balti24.com</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">Please specidy your order</p>
            </div>
        </div>
        <form action="" method="post">

            <select id="serviceArea" name="ServiceArea" class="form-control"><option value disabled>Please Select</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Repair">Repair</option></select></div><br>

            <input type="text" class = "form-control" name="address" id="address" placeholder="Enter your address" required><br>

            <input type="text" class = "form-control" name="city" id="city" placeholder="Enter your city" required><br>

            <div class="form-group"><label for="country">Select Country *</label>
            <select id="country" name="Country" class="form-control"><option value disabled>Please Select</option>
                <option value="Germany">Latvia</option>
                <option value="Latvia">Germany</option></select></div>

            <div class="form-group"><label for="date">Select an Available Date *</label>
                <input id="date" type="date" name="Date" class="form-control" value="" /></div> 
                
            <div class="form-group"><label for="taskDescription">Describe the Task *</label>
                <textarea id="taskDescription" placeholder="Provide details about the task" name="TaskDescription" class="form-control"></textarea></div>    

            <div class="form-group"><label for="date">Additional Details (optional)</label>
            <input type="text" class = "form-control" name="details" id="details" placeholder="For example, access codes, parking, etc." required><br>

            <<div class="form-group"><label>Attach a Photo</label>
                <input class="form-control is-invalid" type="file" /></div>
        
            
            <div class="form-group"><div><input id="agreement1" type="checkbox" name="orderModel.AgreeToTerms" class="valid" value="True" />
                    <label for="agreement1">I accept the terms and conditions *</label></div>
                <div><input id="agreement2" type="checkbox" name="orderModel.AgreeToRefundPolicy" class="valid" value="True" />
                    <label for="agreement2">I accept the refund policy *</label></div></div>

            <button class="btn btn-success" type="submit">Submit order</button>
            </form>
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">Other features in development and will be available soon.</p>
            </div>
        </div>
    </div>
</body>
</html>