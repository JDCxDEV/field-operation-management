<table>
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