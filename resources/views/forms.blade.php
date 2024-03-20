<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Submit</title>
  <!-- Include your CSS links here -->
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

  <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>

  <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
</head>


<body>
	<form enctype="multipart/form-data">

		<div class="card-body">

			<div class="row">
				<div class="form-group col-6">
	                <label>Name</label>
	                <input type="text" class="form-control" placeholder="Enter Your Name">
	            </div>

	            <div class="form-group col-6">
	                <label>Relation Ship</label>
	                  <select class="custom-select form-control-border" id="exampleSelectBorder">
	                    <option>Value 1</option>
	                    <option>Value 2</option>
	                    <option>Value 3</option>
	                  </select>                
	            </div>

        	</div>

            <div class="row">
	            <div class="form-group col-6">
	                <label>Father's Name</label>
	                <input type="text" class="form-control" placeholder="Enter Your Father Name">
	            </div>

	             <div class="form-group col-6">
	                <label>Mother's Name</label>
	                <input type="text" class="form-control" placeholder="Enter Your Mother Name">
	            </div>
			</div>

			 <div class="row">
        
	            <div class="form-group col-6">
	            	<label>Gender</label>
	            	<div class="form-check">
	            		<input class="form-check-input" type="radio" name="radio1">
	            		<label class="form-check-label"> <i class="fa fa-male text-info" aria-hidden="true"></i> &nbsp; Male</label>
	            	</div>
	            	<div class="form-check">
	            		<input class="form-check-input" type="radio" name="radio1">
	            		<label class="form-check-label"> <i class="fa fa-female text-info" aria-hidden="true"></i> &nbsp; Female</label>
	            	</div>
	            </div>
	            
	            <div class="form-group col-6">
	             	<label for="date">Date of Birth:</label>
	  				<input type="date" class="form-control" id="date" name="date"> 
	            </div>

        	</div>

            <div class="row">
	            <div class="form-group col-6">
	                <label>Mobile Number</label>
	                <input type="text" class="form-control" placeholder="Enter Your Mobile Number">
	            </div>

				<div class="form-group col-6">
					<label for="exampleInputEmail1">Email address</label>
					<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
				</div>
			</div>

			
			<div class="form-group">
				<label>Address</label>
				<textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>
			</div>

			<div class="row">
				<div class="col-4">
					<div class="form-group">
	                  <label for="exampleSelectBorder">State</label>
	                  <select class="custom-select form-control-border" id="exampleSelectBorder">
	                    <option>Value 1</option>
	                    <option>Value 2</option>
	                    <option>Value 3</option>
	                  </select>
	                </div>
				</div>
				<div class="col-4">
					<div class="form-group">
	                  <label for="exampleSelectBorder">District</label>
	                  <select class="custom-select form-control-border" id="exampleSelectBorder">
	                    <option>Value 1</option>
	                    <option>Value 2</option>
	                    <option>Value 3</option>
	                  </select>
	                </div>
				</div>
				<div class="col-4">
					<div class="form-group">
	                    <label for="exampleSelectBorder">Pincode</label>
	               		<input type="text" class="form-control" placeholder="Enter Your Pincode">
	                </div>
				</div>
			</div>

			<div class="row d-none">
				<div class="form-group col-6">
					<div class="form-group">
						<label for="exampleInputEmail1">married Status</label>
						<input type="checkbox" data-toggle="switchbutton" checked data-size="xs">
					</div>
				</div>

				<div class="form-group col-6">
	                <label>Spouse Name</label>
	                <input type="text" class="form-control" placeholder="Enter Your Wife Name">
	            </div>
			</div>


			<div class="form-group">
				<label for="exampleInputFile">Photos</label>
				<div class="input-group">
					<div class="custom-file">
						<input type="file" class="custom-file-input" id="exampleInputFile">
						<label class="custom-file-label" for="exampleInputFile">Choose file</label>
					</div>
					<div class="input-group-append">
						<span class="input-group-text">Upload</span>
					</div>
				</div>
			</div>
		</div>


	</form>
</body>
</html>