<?php
/* =====================================================
   JOS_CalendarAbout.php -- shared "About" partial
   Ported from the JewishGen InfoFile "Introduction to
   the Jewish Calendar" (m_calint.htm). Included in the
   #about section of BOTH josdates.php and josfest.php
   (pattern B: full text at the bottom of each tool).
   Hebrew is rendered with Unicode numeric entities
   (ASCII-safe) in place of the legacy letter GIFs; the
   standalone-doc table-of-contents and repeated
   "Back to contents" links are omitted since this is an
   embedded explainer, not a standalone page.
   Mugdan copyright preserved per attribution.
   CHW
   ===================================================== */
?>
<section class="jos-about" id="about" tabindex="-1" aria-labelledby="about-heading">
    <p class="jos-about__eyebrow">About the Jewish Calendar</p>
    <h2 id="about-heading">Introduction to the Jewish Calendar</h2>

    <h3>Days and Weeks</h3>
    <p>The Jewish day begins at sunset. The status of the period between
    sunset (the disappearance of the sun behind the horizon) and nightfall
    (the emergence of three medium-sized stars) is doubtful. For some
    purposes it is treated as part of the previous day &mdash; for example at
    the end of Shabbat, when the prohibition of creative activities
    (<i>melacha</i>) remains in force until nightfall.</p>

    <p>Books and computer programs for conversion between the Jewish and
    Gregorian (civil) calendars are based on the daylight portion of the
    Jewish day. For instance, if you know that an ancestor was born on 26
    Nisan 5580, you will find that this corresponds to 10 April 1820 &mdash;
    but the actual birthday may have been the previous evening, 9 April
    1820.</p>

    <p>With the exception of Shabbat, the weekdays have no names; they are
    simply numbered:</p>
    <ol>
        <li><span class="jos-hebrew" lang="he">&#1488;</span> &mdash;
        <i>yom rishon</i> = &ldquo;first day&rdquo; (Sunday)</li>
        <li><span class="jos-hebrew" lang="he">&#1489;</span> &mdash;
        <i>yom sheni</i> = &ldquo;second day&rdquo; (Monday)</li>
        <li><span class="jos-hebrew" lang="he">&#1490;</span> &mdash;
        <i>yom sh'lishi</i> = &ldquo;third day&rdquo; (Tuesday)</li>
        <li><span class="jos-hebrew" lang="he">&#1491;</span> &mdash;
        <i>yom revi'i</i> = &ldquo;fourth day&rdquo; (Wednesday)</li>
        <li><span class="jos-hebrew" lang="he">&#1492;</span> &mdash;
        <i>yom chamishi</i> = &ldquo;fifth day&rdquo; (Thursday)</li>
        <li><span class="jos-hebrew" lang="he">&#1493;</span> &mdash;
        <i>yom shishi</i> = &ldquo;sixth day&rdquo; (Friday)</li>
    </ol>
    <p>The week culminates in the seventh day, the Holy Shabbat
    (<i>shabbat kodesh</i>).</p>

    <h3>Months</h3>
    <p>The Jewish month is based on the lunar (synodic) month, the time it
    takes for the moon to circle the earth. Since one revolution is a little
    over 29.5 days, the length of the months normally alternates between 29
    and 30 days. A month of 30 days is called <i>male</i> (&ldquo;full&rdquo;),
    one of 29 days <i>chaser</i> (&ldquo;defective&rdquo;). Two months are
    <i>male</i> in some years and <i>chaser</i> in others.</p>

    <p>The month begins with the appearance of the new moon. In the time of
    the Temple, the <i>Sanhedrin</i> (the highest court) sanctified the new
    month once two witnesses had sighted the moon. In the middle of the
    fourth century C.E., a fixed calendar was introduced.</p>

    <p>In the Torah the months are numbered, the first being the one in which
    the Exodus from Egypt occurred (cf. Shemot [Exodus] 12:2). Later, names of
    Babylonian origin were adopted:</p>
    <ol>
        <li><span class="jos-hebrew" lang="he">&#1504;&#1497;&#1505;&#1503;</span> &mdash; <i>Nisan</i> &mdash; (30 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1488;&#1497;&#1497;&#1512;</span> &mdash; <i>Iyyar</i> &mdash; (29 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1505;&#1497;&#1493;&#1503;</span> &mdash; <i>Sivan</i> &mdash; (30 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1514;&#1502;&#1493;&#1494;</span> &mdash; <i>Tammuz</i> &mdash; (29 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1488;&#1489;</span> &mdash; <i>Av</i> &mdash; (30 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1488;&#1500;&#1493;&#1500;</span> &mdash; <i>Elul</i> &mdash; (29 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1514;&#1513;&#1512;&#1497;</span> &mdash; <i>Tishri</i> &mdash; (30 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1495;&#1513;&#1493;&#1503;</span> &mdash; <i>Cheshvan</i> &mdash; (29 or 30 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1499;&#1505;&#1500;&#1493;</span> &mdash; <i>Kislev</i> &mdash; (30 or 29 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1496;&#1489;&#1514;</span> &mdash; <i>Tevet</i> &mdash; (29 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1513;&#1489;&#1496;</span> &mdash; <i>Sh'vat</i> &mdash; (30 days)</li>
        <li><span class="jos-hebrew" lang="he">&#1488;&#1491;&#1512;</span> &mdash; <i>Adar</i> &mdash; (29 days)</li>
    </ol>
    <p>The first day of each month (except <i>Rosh Hashana</i>, the Jewish New
    Year) is <i>Rosh Chodesh</i> (&ldquo;head of the month&rdquo;) &mdash; and
    so is the thirtieth day of the preceding month, if there is one. For
    example, if a gravestone inscription mentions the first day of
    <i>Rosh Chodesh Elul</i>, the calendar date &ldquo;30 Av&rdquo; is
    meant.</p>

    <h3>Years</h3>
    <p>An ordinary year consists of twelve months. When Cheshvan has 29 days
    and Kislev 30, the year is &ldquo;regular&rdquo; (<i>kesidra</i>); if both
    have 30 days it is &ldquo;complete&rdquo; (<i>sh'lema</i>); and if both
    have 29 days it is &ldquo;defective&rdquo; (<i>chasera</i>). Thus an
    ordinary year can have 353, 354, or 355 days.</p>

    <p>A lunar year of 354 days is about 11 days shorter than the solar year.
    If the Jewish calendar were based exclusively on the lunar year, Pesach
    (15 Nisan) would drift through the seasons, returning to spring only after
    about 33 years. But the Torah requires that Pesach be celebrated in the
    spring (cf. Shemot [Exodus] 13:4), so the average length of the Jewish
    year is adjusted to the solar year by adding an entire month roughly every
    three years. In each cycle of 19 years, the 3rd, 6th, 8th, 11th, 14th,
    17th, and 19th years are leap years; the others are common years. For
    example, 5755 was a leap year because it was the 17th year in the 303rd
    cycle of 19 years. (You can check this with the
    <a href="/JOS/mjyear.php">Hebrew Year Converter</a>.)</p>

    <p>The extra month in a leap year has 30 days, so the year lasts 383, 384,
    or 385 days. It is added after Sh'vat and called Adar I, while the original
    Adar (29 days) becomes Adar II. Purim, on 14 Adar, is celebrated in Adar II
    in a leap year. Someone born in Adar of a common year celebrates the
    anniversary in Adar II in leap years, but yahrzeit for someone who died in
    Adar of a common year is observed in Adar I in leap years.</p>

    <p>The new year begins with <i>Rosh Hashana</i>, the first of Tishri
    (although Tishri is the seventh month), in September or early October by
    the civil calendar. Jewish years are counted from the Creation of the
    world. To convert a Jewish year to the Common Era, subtract 3760 (or 3761
    for the first months; in most years, 1 January falls in Tevet). For
    example, the major part of the Jewish year 5678 corresponded to 1918, and
    the beginning of 5678 was in 1917. When the year is written with Hebrew
    letters, the 5000 is usually omitted (the &ldquo;small count&rdquo;); in
    that case the civil equivalent is found by adding 1240. For instance, the
    letters <span class="jos-hebrew" lang="he">&#1496;&#1513;&#1504;&#1493;</span>
    add up to 756, short for 5756 &mdash; the Jewish year corresponding to
    1996 (756 + 1240). (The numerical value of a year written in Hebrew
    letters can be determined with the
    <a href="/JOS/mjyear.php">Hebrew Year Converter</a>.)</p>

    <h3>Holidays</h3>
    <p>All Jewish holidays, fast days, and remembrance days have a fixed date
    in the Jewish calendar. Some are shifted to a different day if they fall on
    or just before Shabbat.</p>

    <h3>Major festivals</h3>
    <p>The Torah describes two cycles of festivals (cf. Vayikra [Leviticus]
    Ch. 23, Bamidbar [Numbers] Ch. 28&ndash;29): the three pilgrimage
    festivals (Pesach, Shavuot, Sukkot) and the High Holidays (Rosh Hashana,
    Yom Kippur).</p>
    <dl>
        <dt><i>Rosh Hashana</i> (New Year)</dt>
        <dd>1&ndash;2 Tishri</dd>
        <dt><i>Yom Kippur</i> (Day of Atonement)</dt>
        <dd>10 Tishri</dd>
        <dt><i>Sukkot</i> (Tabernacles): Full Holiday</dt>
        <dd>Diaspora: 15&ndash;16 Tishri; Israel: 15 Tishri</dd>
        <dt><i>Sukkot: Chol Hamoed</i> (Semi-Holidays)</dt>
        <dd>Diaspora: 17&ndash;21 Tishri; Israel: 16&ndash;21 Tishri</dd>
        <dt><i>Sh'mini Atzeret</i> (Eighth Day of Assembly)</dt>
        <dd>22 Tishri</dd>
        <dt><i>Simchat Tora</i> (Rejoicing of the Tora)</dt>
        <dd>Diaspora: 23 Tishri; Israel: combined with Sh'mini Atzeret (22 Tishri)</dd>
        <dt><i>Pesach</i> (Passover): Full Holiday</dt>
        <dd>Diaspora: 15&ndash;16 Nisan; Israel: 15 Nisan</dd>
        <dt><i>Pesach: Chol Hamoed</i> (Semi-Holidays)</dt>
        <dd>Diaspora: 17&ndash;20 Nisan; Israel: 16&ndash;20 Nisan</dd>
        <dt><i>Pesach:</i> Final Holiday</dt>
        <dd>Diaspora: 21&ndash;22 Nisan; Israel: 21 Nisan</dd>
        <dt><i>Shavuot</i> (Festival of Weeks)</dt>
        <dd>Diaspora: 6&ndash;7 Sivan; Israel: 6 Sivan</dd>
    </dl>

    <h3>Minor festivals</h3>
    <p>Two festivals commemorating the miraculous salvation of the Jewish
    people were instituted after the beginning of the Babylonian exile: Purim
    has its basis in the biblical Book of Esther, Chanukka in the apocryphal
    Books of the Maccabees.</p>
    <dl>
        <dt><i>Chanukka</i> (Festival of Lights)</dt>
        <dd>If Kislev has 30 days: 25 Kislev &ndash; 2 Tevet; if 29 days: 25
        Kislev &ndash; 3 Tevet</dd>
        <dt><i>Purim</i> (Festival of Lots)</dt>
        <dd>14 Adar (in leap years Adar II); <i>Shushan Purim</i> in Jerusalem:
        15 Adar (in leap years Adar II)</dd>
    </dl>

    <h3>Fast days</h3>
    <p>In addition to Yom Kippur and Ta'anit Esther, four public fast days
    commemorating the destruction of the first Temple were instituted in the
    era of the Prophets (cf. Zechariah 8:19). Since fasting is forbidden on
    Shabbat (except Yom Kippur), fast days that fall on Shabbat are shifted.</p>
    <dl>
        <dt><i>Tzom Gedalya</i> (assassination of the governor Gedaliah)</dt>
        <dd>3 Tishri (if on Shabbat, observed Sunday, 4 Tishri)</dd>
        <dt><i>Asara b'Tevet</i> (beginning of the Babylonian siege of Jerusalem)</dt>
        <dd>10 Tevet</dd>
        <dt><i>Ta'anit Ester</i> (Fast of Esther)</dt>
        <dd>13 Adar (in leap years Adar II); if on Shabbat, observed Thursday
        (11 Adar)</dd>
        <dt><i>Shiv'a Asar b'Tammuz</i> (first breach in the walls of Jerusalem)</dt>
        <dd>17 Tammuz (if on Shabbat, observed Sunday, 18 Tammuz)</dd>
        <dt><i>Tish'a b'Av</i> (destruction of the Temple)</dt>
        <dd>9 Av (if on Shabbat, observed Sunday, 10 Av)</dd>
    </dl>

    <h3>Other special days</h3>
    <p>After the proclamation of the State of Israel, new minor festivals and
    memorial days were introduced; Tu bi-Shvat and Lag ba-Omer, which go back
    to Talmudic times, became particularly popular with children.</p>
    <dl>
        <dt><i>Tu bi-Shvat</i> (New Year of Trees)</dt>
        <dd>15 Sh'vat</dd>
        <dt><i>Yom ha-Sho'ah</i> (Holocaust Memorial Day)</dt>
        <dd>27 Nisan</dd>
        <dt><i>Yom ha-Zikkaron</i> (Memorial Day for fallen Israeli soldiers)</dt>
        <dd>Eve of Yom ha-Atzma'ut</dd>
        <dt><i>Yom ha-Atzma'ut</i> (Israel Independence Day)</dt>
        <dd>5 Iyyar (if on Friday or Shabbat, celebrations held Thursday, 4 or
        3 Iyyar, to avoid desecrating Shabbat)</dd>
        <dt><i>Lag ba-Omer</i> (33rd day in the Omer period)</dt>
        <dd>18 Iyyar</dd>
        <dt><i>Yom Yerushalayim</i> (Jerusalem Day)</dt>
        <dd>28 Iyyar</dd>
    </dl>

    <h3>Books</h3>
    <ul>
        <li>Arthur Spier, <a href="http://www.worldcat.org/oclc/14095818"><i>The
        Comprehensive Hebrew Calendar</i></a>, 3rd ed., Spring Valley, NY /
        Jerusalem: Feldheim, 1986. A brief guide with conversion tables for
        5660&ndash;5860 / 1900&ndash;2100 and Shabbat readings.</li>
        <li>R' Nathan Bushwick, <a href="http://www.worldcat.org/oclc/19657896"><i>Understanding
        the Jewish Calendar</i></a>, New York / Jerusalem: Moznaim, 1989. Easy
        to follow, with many examples, tables, and diagrams.</li>
        <li>William Moses Feldman, <a href="http://www.worldcat.org/oclc/35953904"><i>Rabbinical
        Mathematics and Astronomy</i></a>, 4th ed., New York: Sepher-Hermon,
        1991. More technical; analyzes astronomical calculations in the Talmud
        and in Maimonides' <i>Kiddush ha-chodesh</i>.</li>
    </ul>

    <h3>Computer Programs</h3>
    <ul>
        <li><a href="http://www.hebcal.com/"><i>HebCal</i></a> for the civil
        years 1600&ndash;2200, with halakhic times, Shabbat readings, and
        holiday lists.</li>
        <li><a href="http://stevemorse.org/jcal/jcal.html">Jewish Calendar
        Conversions in One Step</a>, by Steve Morse.</li>
    </ul>

    <h3>Related JewishGen Tools</h3>
    <ul>
        <li><a href="/JOS/mjyear.php">Hebrew Year Converter</a> &mdash;
        Hebrew letters / numerals and leap-year check.</li>
        <li><a href="/JOS/josdates.php">Calendar Converter</a> &mdash;
        civil / Hebrew date conversion.</li>
        <li><a href="/JOS/josfest.php">Festival Dates</a> &mdash; Jewish
        holidays for any year.</li>
    </ul>

    <p class="jos-about__attribution">Copyright &copy;1996 Joachim Mugdan, all
    rights reserved. Adapted from the JewishGen InfoFile &ldquo;Introduction to
    the Jewish Calendar.&rdquo;</p>

    <a class="jos-backtop" href="#main-content">&uarr; Back to the tool</a>
</section>
