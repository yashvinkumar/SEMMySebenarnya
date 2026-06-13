{{ $agency->agency_Name }}
<span class="badge {{ $agency->activeAssignmentsCount() > 0 ? 'bg-primary' : 'bg-secondary' }} ms-1"
      title="Active assignments (pending + in progress)">
    {{ $agency->activeAssignmentsCount() }}
</span>
