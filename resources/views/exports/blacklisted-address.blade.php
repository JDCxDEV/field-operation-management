<table>
    <thead>
		<tr>
			<th>street</th>
			<th>city</th>
			<th>state</th>
			<th>zip</th>
		</tr>
    </thead>
    <tbody>
		@foreach($addresses as $address)
			<tr>
				<td>{{ $address->address_1 }}</td>
				<td>{{ $address->city }}</td>
				<td>{{ $address->state }}</td>
				<td>{{ $address->zip }}</td>
			</tr>
		@endforeach
    </tbody>
</table>