export function getHost() {
  switch (process.env.NODE_ENV) {
    case "test":
      return "http://tool.dev.100cbc.com";
      break;
    case "development":
      return "/api";
      break;
    case "production":
      return "https://tool.100cbc.com";
      break;
    default:
      return "/api";
      break;
  }
}
