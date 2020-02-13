#include "udp.h"
#include "config.h"
#include "console.h"

#include <stdio.h>

#include "esp_log.h"

#include "lwip/err.h"
#include "lwip/sockets.h"
#include "lwip/sys.h"
#include <lwip/netdb.h>

static const char *TAG = "example";

typedef struct
{
  int port;
  void (*process)(char const *, size_t);
} LineProcessor;

static void udp_process_linemode(char const *data, size_t length)
{
  console_newline();
  console_write(data, length);
}

static void udp_process_fullrefresh(char const *data, size_t length)
{
  console_clear(false);
  console_write(data, length);
}

static void udp_process_xymode(char const *data, size_t length)
{
  console_setCursor(0, 0);
  console_write(data, length);
}

static LineProcessor lp_linemode = {
    UDP_LINE_API_PORT,
    udp_process_linemode,
};

static LineProcessor lp_fullmode = {
    UDP_FULL_API_PORT,
    udp_process_fullrefresh,
};

static LineProcessor lp_xymode = {
    UDP_XY_API_PORT,
    udp_process_xymode,
};

static void udp_server_task(void *pvParameters)
{
  char rx_buffer[128];
  char addr_str[128];
  int addr_family;
  int ip_protocol;

  LineProcessor const *lineProcessor = pvParameters;

  while (1)
  {
    struct sockaddr_in dest_addr;
    dest_addr.sin_addr.s_addr = htonl(INADDR_ANY);
    dest_addr.sin_family = AF_INET;
    dest_addr.sin_port = htons(lineProcessor->port);
    addr_family = AF_INET;
    ip_protocol = IPPROTO_IP;
    inet_ntoa_r(dest_addr.sin_addr, addr_str, sizeof(addr_str) - 1);

    int sock = socket(addr_family, SOCK_DGRAM, ip_protocol);
    if (sock < 0)
    {
      ESP_LOGE(TAG, "Unable to create socket: errno %d", errno);
      break;
    }
    ESP_LOGI(TAG, "Socket created");

    int err = bind(sock, (struct sockaddr *)&dest_addr, sizeof(dest_addr));
    if (err < 0)
    {
      ESP_LOGE(TAG, "Socket unable to bind: errno %d", errno);
    }
    ESP_LOGI(TAG, "Socket bound, port %d", lineProcessor->port);

    while (1)
    {
      ESP_LOGI(TAG, "Waiting for data");
      struct sockaddr_in6 source_addr; // Large enough for both IPv4 or IPv6
      socklen_t socklen = sizeof(source_addr);
      int len = recvfrom(sock, rx_buffer, sizeof(rx_buffer) - 1, 0, (struct sockaddr *)&source_addr, &socklen);

      // Error occurred during receiving
      if (len < 0)
      {
        ESP_LOGE(TAG, "recvfrom failed: errno %d", errno);
        break;
      }
      // Data received
      else
      {
        // Get the sender's ip address as string
        if (source_addr.sin6_family == PF_INET)
        {
          inet_ntoa_r(((struct sockaddr_in *)&source_addr)->sin_addr.s_addr, addr_str, sizeof(addr_str) - 1);
        }
        else if (source_addr.sin6_family == PF_INET6)
        {
          inet6_ntoa_r(source_addr.sin6_addr, addr_str, sizeof(addr_str) - 1);
        }

        rx_buffer[len] = 0; // Null-terminate whatever we received and treat like a string...
        ESP_LOGI(TAG, "Received %d bytes from %s:", len, addr_str);
        ESP_LOGI(TAG, "%s", rx_buffer);

        lineProcessor->process(rx_buffer, len);

        // int err = sendto(sock, rx_buffer, len, 0, (struct sockaddr *)&source_addr, sizeof(source_addr));
        // if (err < 0)
        // {
        //   ESP_LOGE(TAG, "Error occurred during sending: errno %d", errno);
        //   break;
        // }
      }
    }

    if (sock != -1)
    {
      ESP_LOGE(TAG, "Shutting down socket and restarting...");
      shutdown(sock, 0);
      close(sock);
    }
  }
  vTaskDelete(NULL);
}

void udpapi_init()
{
  xTaskCreate(udp_server_task, "udp_server", 4096, &lp_linemode, 5, NULL);
  xTaskCreate(udp_server_task, "udp_server", 4096, &lp_fullmode, 5, NULL);
  xTaskCreate(udp_server_task, "udp_server", 4096, &lp_xymode, 5, NULL);
}