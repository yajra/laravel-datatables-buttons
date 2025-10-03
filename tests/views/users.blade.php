<h2>{{ $title ?? 'DataTable' }}</h2>
<p>{{ $description ?? 'No description' }}</p>
<div>
    {{ $dataTable->table() }}
</div>

{{ $dataTable->scripts() }}
