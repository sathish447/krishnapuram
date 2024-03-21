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

    <link rel="stylesheet" href="{{ asset('dist/css/form.css') }}">

  <style type="text/css">  </style>
</head>

<body class="formbody">

	<!-- Display any validation errors -->
	@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<form enctype="multipart/form-data" method="post" action="{{ url('form') }}">

		@csrf

		<div class="card-head">
			<h1><center>Krishnapuram Members Form</center></h1>
		</div>

		<div class="card-body">

			<div class="row">
				<div class="form-group col-6">
	                <label>Name <span class="text-danger"> * </span></label>
	                <input type="text" class="form-control input-style" placeholder="Enter Your Name" name="name" id="name">
	                   <!-- Display error message for name field -->
				        @error('name')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
	            </div>

				<div class="form-group col-6">
				    <label>Varisu <span class="text-danger"> * </span></label>
				    <div class="position-relative">
				        <select class="custom-select form-control-border input-style" id="exampleSelectBorder" name="varisu" id="varisu">
				            <option>Arumugam</option>
				            <option>Krishnan</option>
				            <option>Muthu</option>
				        </select>
				        <i class="fa fa-chevron-down position-absolute selectdropcss" ></i>
				    </div>

				      <!-- Display error message for varisu field -->
				        @error('varisu')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
				</div>

        	</div>

            <div class="row">
	            <div class="form-group col-6">
	                <label>Father's Name <span class="text-danger"> * </span></label>
	                <input type="text" class="form-control input-style" placeholder="Enter Your Father Name" name="fname" id="fname">
	                  <!-- Display error message for fname field -->
				        @error('fname')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
	            </div>

	             <div class="form-group col-6">
	                <label>Mother's Name <span class="text-danger"> * </span></label>
	                <input type="text" class="form-control input-style" placeholder="Enter Your Mother Name" name="mname" id="mname">
	                  <!-- Display error message for mname field -->
				        @error('mname')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
	            </div>
			</div>

			 <div class="row">

				<div class="form-group col-6">
				    <label>Gender <span class="text-danger"> * </span></label> &nbsp;
				    <div class="btn-group" data-toggle="buttons">
				        <label class="btn btn-outline-info">
				            <input type="radio" name="gender" value="male"> <i class="fa fa-male" aria-hidden="true"></i> Male
				        </label>
				        <label class="btn btn-outline-info">
				            <input type="radio" name="gender" value="female"> <i class="fa fa-female" aria-hidden="true"></i> Female
				        </label>
				    </div>
				      <!-- Display error message for gender field -->
				        @error('gender')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
				</div>
	            
	            <div class="form-group col-6">
	             	<label for="date">Date of Birth:</label>
	  				<input type="date" class="form-control input-style-calender" id="date" name="date" name="dob" id="dob"> 
	  				  <!-- Display error message for dob field -->
				        @error('dob')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
	            </div>

        	</div>

            <div class="row">
	            <div class="form-group col-6">
	                <label>Mobile Number <span class="text-danger"> * </span></label>
	                <input type="text" class="form-control input-style-mobile" placeholder="Enter Your Mobile Number" name="mobile" id="mobile">
	                  <!-- Display error message for mobile field -->
				        @error('mobile')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
	            </div>

				<div class="form-group col-6">
					<label for="exampleInputEmail1">Email address</label>
					<input type="email" class="form-control input-style-email" id="exampleInputEmail1" placeholder="Enter email" name="email" id="email">
					  <!-- Display error message for email field -->
				        @error('email')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
				</div>
			</div>

			
			<div class="form-group">
				<label>Address</label>
				<textarea class="form-control" rows="3" name="address" id="address" placeholder="Enter ..." style="border-radius: 0.5rem"></textarea>
			</div>

			<div class="row">
				<div class="col-4">
					<div class="form-group">
	                  <label for="exampleSelectBorder">State <span class="text-danger"> * </span></label>
	                  <select class="custom-select form-control-border input-style-location" name="state" id="state">
	                    <option>Tamil Nadu</option>
	                  </select>
	                    <i class="fa fa-chevron-down position-absolute selectdropLoccss"></i>
	                </div>
	                  <!-- Display error message for state field -->
				        @error('state')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
				</div>
				<div class="col-4">
					<div class="form-group">
	                  <label for="exampleSelectBorder">District <span class="text-danger"> * </span></label>
	                  <select class="custom-select form-control-border input-style-location" name="district" id="district">
	                    <option>Madurai</option>
	                  </select>
	                    <i class="fa fa-chevron-down position-absolute selectdropLoccss"></i>
	                </div>
	                  <!-- Display error message for district field -->
				        @error('district')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
				</div>
				<div class="col-4">
					<div class="form-group">
	                    <label for="exampleSelectBorder">Pincode <span class="text-danger"> * </span></label>
	               		<input type="text" class="form-control input-style-location" placeholder="Enter Your Pincode" name="pincode" id="pincode">
	                </div>
	                  <!-- Display error message for pincode field -->
				        @error('pincode')
				            <div class="text-danger">{{ $message }}</div>
				        @enderror
				</div>
			</div>

			<div class="row d-none">
				<div class="form-group col-6">
					<div class="form-group">
						<label for="exampleInputEmail1">married Status</label>
						<input type="checkbox" data-toggle="switchbutton" checked data-size="xs" name="married_status" id="married_status">
					</div>
				</div>

				<div class="form-group col-6">
	                <label>Spouse Name</label>
	                <input type="text" class="form-control" placeholder="Enter Your Wife Name" name="spouse_name" id="spouse_name">
	            </div>
			</div>


	<!-- 		<div class="form-group">
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
 -->
			<div class="form-group">
			  <label for="exampleInputFile">Photos</label>
			  <div class="wrapper">
			    <div class="file-upload custom-file">
			      <input type="file" name="photos" id="photos">
			    </div>
			  </div>

			<div class="form-group mt-4">
				<button class="btn btn-info form-control">  Submit</button>
			</div>
		</div>

	</form>

	<script src="{{ asset('dist/js/custom.js') }}"></script>
</body>
</html>