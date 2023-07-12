export const useChat = () => {
  const resolveAvatarBadgeVariant = status => {
    if (status === 'online')
      return 'success'
    if (status === 'busy')
      return 'error'
    if (status === 'away')
      return 'warning'
    
    return 'secondary'
  }

  return {
    resolveAvatarBadgeVariant,
  }
}
