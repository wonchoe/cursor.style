<html>
    <head>
        <title>Cursor style reports</title>
        <link rel="stylesheet" href="https://demos.creative-tim.com/notus-js/assets/styles/tailwind.css">
        <link rel="stylesheet" href="https://demos.creative-tim.com/notus-js/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">    

    </head>
    <body>
        <style>
            .upper-right {
                position: relative; /* Enable positioning */
            }
            .flex-1{
                background: linear-gradient(45deg, transparent, #c5eee9, transparent);
            }

            .upper-right .comparison-text {
                position: absolute; /* Absolute positioning */
                top: 0; /* Align to the top */
                right: 0; /* Align to the right */
                font-size: 0.8em; /* Adjust font size as needed */
                color: #555; /* Change color as needed */
            }
            
            .gh_work{
                background: linear-gradient(45deg, transparent, #7bfd6c, transparent);
            }
            
            .gh_not_work{
                background: linear-gradient(45deg, transparent, #7bfd6c, transparent);
            }            
        </style>    



        @if ($grub_hub)
        <section class="py-1 bg-blueGray-50">
            <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4 mx-auto mt-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded ">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="flex-1 bg-gradient-to-r gh_work from-cyan-400 to-cyan-600 rounded-lg flex flex-col items-center justify-center p-4 space-y-2 border border-gray-200 m-2">
                                <h3 class="text-2xl font-bold text-gray-600">GRUBHUB WORK!</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @else
        <section class="py-1 bg-blueGray-50">
            <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4 mx-auto mt-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded ">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="flex-1 bg-gradient-to-r gh_not_work from-cyan-400 to-cyan-600 rounded-lg flex flex-col items-center justify-center p-4 space-y-2 border border-gray-200 m-2">
                                <h3 class="text-2xl font-bold text-gray-600">GRUBHUB NOT WORK!</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif            

        @foreach($projects as $project => $data)
        <section class="py-1 bg-blueGray-50">
            <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4 mx-auto mt-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded ">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="flex-1 bg-gradient-to-r from-cyan-400 to-cyan-600 rounded-lg flex flex-col items-center justify-center p-4 space-y-2 border border-gray-200 m-2">
                                <h3 class="text-2xl font-bold text-gray-600">{{ $data->first()->project_name }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="block w-full overflow-x-auto">
                        <table class="items-center bg-transparent w-full border-collapse ">
                            <thead>
                                <tr>
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Date
                                    </th>
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Installs
                                    </th>
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Uninstalls
                                    </th>
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Uninstalls rate
                                    </th>                                    
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Users total
                                    </th>
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Rating
                                    </th>                                    
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Feedbacks
                                    </th>
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Overal rank
                                    </th>                                    
                                    <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Category rank
                                    </th>                                          
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($data as $report)
                                <tr>
                                    <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left text-blueGray-700 ">
                                        {{ \Carbon\Carbon::parse($report->date)->format('Y-m-d') }}
                                    </th>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 ">
                                        {{ $report->installs }}
                                    </td>
                                    <td class="border-t-0 px-6 align-center border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        {{ $report->uninstalls }}
                                    </td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        {{ $report->uninstall_rate }}
                                    </td>                                    
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        {{ $report->users_total }}
                                    </td>
                                    <td class="border-t-0 px-6 align-center border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        {{ $report->rating_value }}
                                    </td>          
                                    <td class="border-t-0 px-6 align-center border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        @if(isset($report->feedbacks_sign))
                                        <span>{!! $report->feedbacks_sign !!}</span>
                                        @endif               

                                        {{ $report->feedbacks_total }}

                                        @if(isset($report->feedbacks_total_comparison))
                                        <span><sup>{{ $report->feedbacks_total_comparison }}</sup></span>
                                        @endif
                                    </td>
                                    <td class="border-t-0 px-6 align-center border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        @if(isset($report->overal_rank_sign))
                                        <span>{!! $report->overal_rank_sign !!}</span>
                                        @endif      

                                        {{ $report->overal_rank }}

                                        @if(isset($report->overal_rank_comparison))
                                        <span><sup>{{ $report->overal_rank_comparison }}</sup></span>
                                        @endif
                                    </td>                                    
                                    <td class="border-t-0 px-6 align-center border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        @if(isset($report->cat_rank_sign))
                                        <span>{!! $report->cat_rank_sign !!}</span>
                                        @endif

                                        {{ $report->cat_rank }}

                                        @if(isset($report->cat_rank_comparison))
                                        <span><sup>{{ $report->cat_rank_comparison }}</sup></span>
                                        @endif
                                    </td>                                         
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </section>
        @endforeach









    </body>
</html>