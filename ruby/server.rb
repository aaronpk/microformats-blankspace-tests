require 'sinatra'
require 'microformats'

post "/parse" do
  doc = Microformats.parse(params[:html])
  JSON.pretty_generate(doc.to_h)
end
