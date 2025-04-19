<html>
    <head>
        <title>Cursor style reports</title>
        <link rel="stylesheet" href="/css/admin/tailwind.css">
        <link rel="stylesheet" href="/css/admin/all.min.css">    

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
                background: linear-gradient(45deg, transparent, #ff4d4d, transparent);
            }            
        </style>    

<script>
    document.getElementById('loadBtn').addEventListener('click', async () => {
      const container = document.getElementById('tableContainer');
      container.innerHTML = 'Loading...';

      try {
        const response = await fetch('https://i6bnl4iutvwekmi6ziw5vi7bxi0hwclp.lambda-url.us-east-1.on.aws');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();

        if (!data.feedbacks || data.feedbacks.length === 0) {
          container.innerHTML = '<p>No feedback available.</p>';
          return;
        }

        const table = document.createElement('table');
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        ['Message', 'Created At'].forEach(text => {
          const th = document.createElement('th');
          th.textContent = text;
          headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        const tbody = document.createElement('tbody');
        data.feedbacks.forEach(item => {
          const row = document.createElement('tr');

          const messageCell = document.createElement('td');
          messageCell.textContent = item.message;
          row.appendChild(messageCell);

          const dateCell = document.createElement('td');
          const date = new Date(parseInt(item.createdAt));
          dateCell.textContent = date.toLocaleString();
          dateCell.classList.add('timestamp');
          row.appendChild(dateCell);

          tbody.appendChild(row);
        });

        table.appendChild(tbody);
        container.innerHTML = '';
        container.appendChild(table);
      } catch (error) {
        container.innerHTML = `<p style="color: red;">Error loading feedback: ${error.message}</p>`;
      }
    });
  </script>


        <section class="py-1 bg-blueGray-50">
            <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4 mx-auto mt-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded ">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="flex-1 bg-gradient-to-r {{ $grub_hub ? 'gh_work' : 'gh_not_work' }} from-cyan-400 to-cyan-600 rounded-lg flex flex-col items-center justify-center p-4 space-y-2 border border-gray-200 m-2">
                                <h3 class="text-2xl font-bold text-gray-600">{{ $grub_hub ? 'GRUBHUB WORK!' : 'GRUBHUB NOT WORK!' }}</h3>
				<h1>Last check: {{ (new DateTime($grubhub_schedule_response['current_datetime']))->format('d-m-Y H:i:s') }}</h1>
				<h1>Grubhub updates: {{ (new DateTime($grubhub_schedule_response['last_updated_date']))->format('d-m-Y H:i:s') }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>          


        <button id="loadBtn">Load Feedback</button>
        <div id="tableContainer"></div>
        
        
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
