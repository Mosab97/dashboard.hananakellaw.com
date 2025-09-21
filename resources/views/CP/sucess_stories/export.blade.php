<table>
    <thead>
        <tr>
            <th class="min-w-100px bold all">{{ __('SN') }}</th>
            <th class="min-w-100px bold all">{{ t('ID') }}</th>
            <th class="min-w-200px bold all">{{ t('Name') }}</th>
            <th class="min-w-200px bold all">{{ t('Description') }}</th>
            <th class="min-w-150px bold all">{{ t('Deadline') }}</th>
            <th class="min-w-200px bold all">{{ t('How to Apply') }}</th>
            <th class="min-w-150px bold all">{{ t('Target Applicant') }}</th>
            <th class="min-w-150px bold all">{{ t('Category') }}</th>
            <th class="min-w-100px bold all">{{ t('Fund') }}</th>
            <th class="min-w-150px bold all">{{ t('Important Dates') }}</th>
            <th class="min-w-150px bold all">{{ t('Eligibilities') }}</th>
            <th class="min-w-150px bold all">{{ t('Facilities') }}</th>
            <th class="min-w-150px bold all">{{ t('Created At') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ ++$loop->index }}</td>
                <td>{{ $item->id ?? 'NA' }}</td>
                <td>{{ $item->getTranslation('name', app()->getLocale()) ?? 'NA' }}</td>
                <td>{{ $item->getTranslation('description', app()->getLocale()) ?? 'NA' }}</td>
                <td>{{ $item->deadline ? $item->deadline->format('Y-m-d') : 'NA' }}</td>
                <td>{{ $item->getTranslation('how_to_apply', app()->getLocale()) ?? 'NA' }}</td>
                <td>{{ optional($item->target_applicant)->name ?? 'NA' }}</td>
                <td>{{ implode(',',$item->categories->pluck('name')->toArray()) ?? 'NA'}}</td>
                <td>{{ $item->fund ?? 'NA' }}</td>
                <td>
                    @if ($item->important_dates->count() > 0)
                        @foreach ($item->important_dates as $date)
                            {{ $date->title }}: {{ $date->date->format('Y-m-d') }}
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    @else
                        NA
                    @endif
                </td>
                <td>
                    @if ($item->eligibilities->count() > 0)
                        {{ $item->eligibilities->pluck('name')->implode(', ') }}
                    @else
                        NA
                    @endif
                </td>
                <td>
                    @if ($item->facilities->count() > 0)
                        {{ $item->facilities->pluck('name')->implode(', ') }}
                    @else
                        NA
                    @endif
                </td>
                <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
