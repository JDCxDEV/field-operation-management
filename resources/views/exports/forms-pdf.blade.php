<!DOCTYPE html>
<html>
<head>
	<title>Lead Form</title>
</head>
<style>

* {
	font-family: "Roboto";
}

html {
	margin: 0;
}

body {
	font-size: 12px;
	margin: 30px;
}

body, h1, h2, h3, h4, h5, h6, div {
	line-height: 1.1;
}

table, th, td {
	border: 1px solid black;
	border-collapse: collapse;
	padding: 5px;
}

th {
	text-align: left;
}

</style>
<body>
	<table style="width: 100%">
		<thead>
			<tr>
				<th>Application ID</th>
				<th>Created At</th>
				<th>Agent Name</th>
				<th>Client Name</th>
				<th>Client Phone</th>
				<th>Client Email</th>
				<th>Client State</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			@foreach($forms as $form)
				<tr>
					<td>{{ $form->application_id }}</td>
					<td>{{ $form->create_at_readable }}</td>
					<td>{{ $form->creator_name }}</td>
					<td>{{ $form->renderClientName() }}</td>
					<td>{{ $form->renderClientPhone() }}</td>
					<td>{{ $form->client_email }}</td>
					<td>{{ $form->client_state }}</td>
					<td>{{ optional($form->form_status)->status ? optional($form->form_status)->status : "Draft" }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>