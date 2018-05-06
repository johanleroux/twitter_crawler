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
                @if(isset($leader))
                    <a href="{{ url('/') }}" class="btn text-info btn-outline-info mb-2 mr-2">&#64;{{ $leader->screen_name}}'s following</a>
                @endif
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
        <div class="col-4 text-center text-white">
            {{ $info->resources->friends->{"/friends/list"}->remaining }} requests remaining. Resets in {{ (\Carbon\Carbon::createFromTimestamp($info->resources->friends->{"/friends/list"}->reset))->diffForHumans() }}
        </div>
        <div class="form-inline col-4 justify-content-end">
            <label class="sr-only" for="description">Description</label>
            <input type="text" class="form-control mb-2 mr-2" id="description" name="screen_name" placeholder="Description">

            <button onclick="description(this)" class="btn btn-outline-success mb-2 mr-2">Search</button>
            <button onclick="document.getElementById('followers').submit();" class="btn btn-outline-danger mb-2">Update Crawler</button>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col p-0">
                <form id="followers" action="{{ url('/') }}" method="POST">
                    @csrf
                    @method('patch')
                    <table class="table table-responsive table-dark table-hover mb-0">
                        <thead>
                            <th class="text-center">#</th>
                            <th>Name</th>
                            <th>Screen Name</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th style="min-width: 170px;">Updated At</th>
                            <th class="text-center">
                                Crawl?
                                <input type="checkbox" onchange="toggleCheckbox(this)">
                            </th>
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
                                    <input class="position-static should_crawl" name="crawl[{{ $user->id }}]" type="checkbox" value="1" @if( $user->should_crawl ) checked @endif>
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
                    {{ $users->appends(['user' => $leader ? $leader->id : '', 'search' => $search ?: ''])->links() }}
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCheckbox(element) {
            let checkboxes = document.getElementsByClassName("should_crawl");
            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = element.checked;
            }
        }

        function description(element) {
            let search = document.getElementById("description").value;

            const url = new URL(window.location);
            let user = url.searchParams.get("user");
            let page = url.searchParams.get("page");

            let query = {
                user: user,
                page: page,
                search: search
            }

            let querystring = encodeData(query);

            window.location = '?' + querystring;
        }

        function encodeData(data) {
            return Object.keys(data).map(function(key) {
                if(!data[key]) data[key] = '';
                return [key, data[key]].map(encodeURIComponent).join("=");
            }).join("&");
        }
    </script>
</body>

</html>