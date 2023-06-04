@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Tickets') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Tickets') }}</li>
@endsection

@section('multiple-action-button')
    <div class="row justify-content-end">
        <div class="col-md-4">                
            <select class="form-select" id="projects" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                <option value="{{route('admin.tickets.index')}}">{{__('All Tickets')}}</option>
                <option value="{{route('admin.tickets.index', 'in-progress')}}" @if($status == 'in-progress') selected @endif>{{__('In Progress')}}</option>
                <option value="{{route('admin.tickets.index', 'on-hold')}}" @if($status == 'on-hold') selected @endif>{{__('On Hold')}}</option>
                <option value="{{route('admin.tickets.index', 'closed')}}" @if($status == 'closed') selected @endif>{{__('Closed')}}</option>
            </select>
        </div>
        <div class="col-auto">
            @can('create-tickets')
                <div class="btn btn-sm btn-primary btn-icon m-1 float-end ms-2"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Ticket')}}">
                    <a href="{{route('admin.tickets.create')}}" class=""><i class="ti ti-plus text-white"></i></a>
                </div>
            @endcan
            <div class="btn btn-sm btn-primary btn-icon m-1 ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Export Tickets CSV file')}}">
                <a href="{{route('tickets.export')}}" class="" ><i class="ti ti-file-export text-white"></i></a>            
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            @if(session()->has('ticket_id') || session()->has('smtp_error'))
                <div class="alert alert-info">
                    @if(session()->has('ticket_id'))
                        {!! Session::get('ticket_id') !!}
                        {{ Session::forget('ticket_id') }}
                    @endif
                    @if(session()->has('smtp_error'))
                        {!! Session::get('smtp_error') !!}
                        {{ Session::forget('smtp_error') }}
                    @endif
                </div>
            @endif
        </div>
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Ticket ID') }}</th>
                                <th class="w-10">{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-end me-3">{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tickets as $index => $ticket)
                                <tr>
                                    <th scope="row">{{++$index}}</th>
                                    <td class="Id sorting_1">
                                        <a class="btn btn-outline-primary" href="{{route('admin.tickets.edit',$ticket->id)}}">
                                            {{$ticket->ticket_id}}
                                        </a>
                                    </td>
                                    <td><span class="white-space">{{$ticket->name}}</span></td>
                                    <td>{{$ticket->email}}</td>
                                    <td><span class="white-space">{{$ticket->subject}}</span></td>
                                    <td><span class="badge badge-white p-2 px-3 rounded fix_badge" style="background: {{$ticket->color}};">{{$ticket->category_name}}</span></td>
                                    <td><span class="badge fix_badge @if($ticket->status == 'In Progress')bg-warning @elseif($ticket->status == 'On Hold') bg-danger @else bg-success @endif  p-2 px-3 rounded">{{__($ticket->status)}}</span></td>
                                    <td>{{$ticket->created_at->diffForHumans()}}</td>
                                    <td class="text-end">
                                        @can('reply-tickets')
                                            <div class="action-btn bg-info ms-2">
                                                <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Reply') }}"> <span class="text-white"> <i class="ti ti-corner-up-left"></i></span></a>
                                            </div>
                                        @endcan
                                        @can('delete-tickets')
                                            <div class="action-btn bg-danger ms-2">
                                                <form method="POST" action="{{route('admin.tickets.destroy',$ticket->id)}}" id="user-form-{{$ticket->id}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input name="_method" type="hidden" value="DELETE">
                                                    <button type="submit" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-toggle="tooltip"
                                                    title='Delete'>
                                                        <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                    </button>
                                                </form>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
