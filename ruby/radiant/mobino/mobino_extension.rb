require 'yaml'

class MobinoExtension < Radiant::Extension
  version "1.0"
  description "Creates a tag that can be used to call a mobino widget"
  url "http://mobino.com"


  def activate
    # needs the Signum gem (http://github.com/jcfischer/signum )
    require 'signum'
    Page.class_eval {
      include MobinoTags
    }
  end

end
