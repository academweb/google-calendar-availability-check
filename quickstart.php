
<?php 


class ics {
    /* Function is to get all the contents from ics and explode all the datas according to the events and its sections */
    function getIcsEventsAsArray($file) {
        $icalString = file_get_contents ( $file );
        $icsDates = array ();
        /* Explode the ICs Data to get datas as array according to string ‘BEGIN:’ */
        $icsData = explode ( "BEGIN:", $icalString );
        /* Iterating the icsData value to make all the start end dates as sub array */
        foreach ( $icsData as $key => $value ) {
            $icsDatesMeta [$key] = explode ( "\n", $value );
        }
        /* Itearting the Ics Meta Value */
        foreach ( $icsDatesMeta as $key => $value ) {
            foreach ( $value as $subKey => $subValue ) {
                /* to get ics events in proper order */
                $icsDates = $this->getICSDates ( $key, $subKey, $subValue, $icsDates );
            }
        }
        return $icsDates;
    }

    /* funcion is to avaid the elements wich is not having the proper start, end  and summary informations */
    function getICSDates($key, $subKey, $subValue, $icsDates) {
        if ($key != 0 && $subKey == 0) {
            $icsDates [$key] ["BEGIN"] = $subValue;
        } else {
            $subValueArr = explode ( ":", $subValue, 2 );
            if (isset ( $subValueArr [1] )) {
                $icsDates [$key] [$subValueArr [0]] = $subValueArr [1];
            }
        }
        return $icsDates;
    }
}

$post_date = $_POST['date'];


echo checkCaledar("https://calendar.google.com/calendar/ical/vera7nedvyzhenko%40gmail.com/public/basic.ics", $post_date) + 0 . '<br>';

function checkCaledar($ical, $date){
    $obj = new ics();
    $icsEvents = $obj->getIcsEventsAsArray($ical);
    $result = true;

    if(strtotime($date) < time()){
        $result = false;
    }

    if(intval(date("w", strtotime($date))) < 5 && intval(date("w", strtotime($date))) > 0){
        $result = false;
    }

    if(checkHolidays("https://calendar.google.com/calendar/ical/ru.ukrainian%23holiday%40group.v.calendar.google.com/public/basic.ics", $date)){
        $result = true;
    }

    foreach( $icsEvents as $icsEvent){
        $start = isset( $icsEvent ['DTSTART;VALUE=DATE'] ) ? $icsEvent ['DTSTART;VALUE=DATE'] : $icsEvent ['DTSTART'];
        $startDate = date("m/d/y", strtotime($start));

        if(strtotime($date) == strtotime($startDate)){
            $result = false;
        }
    }

    return $result;
}

function checkHolidays($ical, $date){
    $obj = new ics();
    $icsEvents = $obj->getIcsEventsAsArray($ical);
    $result = false;

    foreach( $icsEvents as $icsEvent){
        $start = isset( $icsEvent ['DTSTART;VALUE=DATE'] ) ? $icsEvent ['DTSTART;VALUE=DATE'] : $icsEvent ['DTSTART'];
        $startDate = date("m/d/y", strtotime($start));

        if(strtotime($date) >= strtotime($startDate) - 3600 * 24 && strtotime($date) <= strtotime($startDate)){
            $result = true;
        }
    }

    return $result;
}