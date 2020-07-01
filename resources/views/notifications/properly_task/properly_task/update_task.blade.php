@foreach($view_data as $key => $properly_task)
The  {{ $properly_task['task_type']}} 's {{$key}} has been modified for {{ $properly_task['entity_id']}}
@endforeach