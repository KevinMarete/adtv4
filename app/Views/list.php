<!DOCTYPE html>
<html>
<head>
	<title>patient list</title>
	
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

</head>
<body>
<div class ="container">
	<h1> List</h1>
	<table class = "table table-striped">
		<thead>
			<th scope ="col"> id </th>
            <th scope ="col"> medical record number </th>
			<th scope ="col"> first name </th>
			<th scope ="col"> last name </th>
</thead>
<?php $i = 0; foreach ($table as $k) { ?>
	<tr>
		<th scope="row"> <?php echo $i++ ;?></th>
		<td><?php echo $k ['medical_record_number']; ?></td>
		<td><?php echo $k ['first_name']; ?></td>
		<td><?php echo $k ['last_name']; ?></td>
	</tr>
	<?php} ?>

}
<tbody>>
 <tr>
<th scope = "row"></th>>

  </tr>
</tbody>

</table>
</div>
</body>
</html>