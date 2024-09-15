<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Email Template</title>
</head>

<body style="margin: 0; padding: 0; background-color: #CBD5E1;">

  <table role="presentation" width="100%" height="100%"
    style="background-color: #e3e9f0; height: 100vh; border-collapse: collapse;">
    <tr>
      <td align="center" valign="middle">
        <div
          style="border-radius: 6px; background-color: #fff; padding: 24px; box-shadow: #959DA5 0px 8px 24px; max-width: 400px; width: 100%;">
          <h1
            style="font-size: 30px; font-weight: 600; color: #38BDF8; text-align: center;">
            Cineverse</h1>
          <div style="color: #000;">
            <p style="text-align: left;">Xin chào 👋, Cảm ơn bạn đã sử dụng
              dịch vụ của chúng tôi.</p>
            <p style="text-align: left;">Phim: <span
                style="font-weight: 600;">{{ $filmName }}</span>
            </p>
            <p style="text-align: left;">Xuất chiếu: <span
                style="font-weight: 600;">{{ $showtime }}</span></p>
            <p style="text-align: left;">Số ghế: <span
                style="font-weight: 600;">{{ $seats }}</span>
            </p>
            <p style="text-align: left;">Phòng chiếu: <span
                style="font-weight: 600;">{{ $auditoriumName }}</span>, Rạp:
              <span style="font-weight: 600;">{{ $cinemaName }}</span>
            </p>
          </div>
        </div>
      </td>
    </tr>
  </table>

</body>

</html>
