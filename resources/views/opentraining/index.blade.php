<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <span><i class="fa fa-table"></i></span>
                    <span>Список тренировок1</span>
                </h3>
            </div>

            <div class="box-body">


                <table id="tbl-list" data-server="false" class="dt-table table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th> ID</th>
                        <th>Название</th>
                        <th>Дата</th>
                        <th>Стиль</th>
                        <th>Тренер </th>
                        <th>Адрес</th>
                        <th>lat</th>
                        <th>long</th>

                        <th style="min-width: 100px;">действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($training as $item)
                        <tr>

                            <td>{{ $item->name }}</td>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->style }}</td>
                            <td>{{ $item->coach }}</td>
                            <td>{{ $item->address }}</td>

                            <td>{{ $item->lat_map }}</td>
                            <td>{{ $item->long_map }}</td>
                            <td>

                            </td>
                         </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
