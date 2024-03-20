@extends('layouts.admin')

@section('title', 'Your Page Title')

@section('content')
 
<!-- <div class="row mb-2">
  <div class="col-sm-6">
    <h1>DataTables</h1>
  </div>
  <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"><a href="#">Home</a></li>
      <li class="breadcrumb-item active">DataTables</li>
    </ol>
  </div>
</div> -->

<div class="row mt-5">
  <div class="col-12">

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Members</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Mobile</th>
            <th>Relation</th>
            <th>Gender</th>
            <th>DoB</th>
            <th>Material Status</th>
            <th>Created At</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>Sathish</td>
            <td>7904982698</td>
            <td>Pangalie</td>
            <td>Male</td>
            <td>06-12-1993</td>
            <td>Maried</td>
            <td>20-03-2024</td>
          </tr>
          <tr>
            <td>Arun Kumar</td>
            <td>7904982698</td>
            <td>Pangalie</td>
            <td>Male</td>
            <td>06-12-1993</td>
            <td>Maried</td>
            <td>20-03-2024</td>
          </tr>
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
@endsection

@section('style')
<style type="text/css">
</style>
@endsection

@section('head-script')
@endsection

@section('scripts')
<!-- Include your page-specific JavaScript here -->
<script>
  $(document).ready(function () {
    $('#example1').DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
@endsection
