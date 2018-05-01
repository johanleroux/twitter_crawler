<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Twitter Follower</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar sticky-top navbar-dark bg-dark">
        <div class="col-4">
            <form action="{{ url('/') }}" method="POST" class="form-inline">
                @csrf
                <label class="sr-only" for="username">Username</label>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-white">@</div>
                    </div>
                    <input type="text" class="form-control" id="username" name="screen_name" placeholder="Username">
                </div>
                <button type="submit" class="btn btn-outline-primary mb-2">Crawl</button>
            </form>
        </div>
        <div class="col-4 text-center">
            @if(isset($leader))
                <a href="{{ url('/') }}" class="btn text-info btn-outline-info my-2 my-sm-0">&#64;{{ $leader->screen_name}}'s followers</a>
            @endif
        </div>
        <div class="col-4 text-right">
            <button onclick="document.getElementById('followers').submit();" class="btn btn-outline-success my-2 my-sm-0">Update Crawler</button>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col p-0">
                <form id="followers" action="{{ url('/') }}" method="POST">
                    @csrf
                    @method('patch')
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <th class="text-center">#</th>
                            <th>Name</th>
                            <th>Screen Name</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th style="min-width: 170px;">Updated At</th>
                            <th class="text-center">Crawl?</th>
                            <th style="min-width: 170px;">Crawled At</th>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="text-center">
                                    <a class="text-white" href="{{ url('/?user=' . $user->id) }}">{{ $user->id }}</a>
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    <a class="text-white" href="{{ Twitter::linkUser($user) }}">&#64;{{ $user->screen_name }}</a>
                                </td>
                                <td>{{ $user->description }}</td>
                                <td>{{ $user->location }}</td>
                                <td>{{ $user->updated_at }}</td>
                                <td class="text-center">
                                    <input class="position-static" name="crawl[{{ $user->id }}]" type="checkbox" value="1" @if( $user->should_crawl ) checked @endif>
                                </td>
                                <td>{{ $user->crawled_at }}</td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No followers...</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</body>

</html>